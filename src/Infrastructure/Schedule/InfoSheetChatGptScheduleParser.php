<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Schedule;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use JsonException;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetCacheUsedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetChatGptApiCalledEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use Throwable;

final readonly class InfoSheetChatGptScheduleParser
{
    private const string OPENAI_FILES_URL = 'https://api.openai.com/v1/files';
    private const string OPENAI_RESPONSES_URL = 'https://api.openai.com/v1/responses';
    private const string OPENAI_FILE_DELETE_URL = 'https://api.openai.com/v1/files/%s';
    private const string OPENAI_FILE_PURPOSE = 'user_data';
    private const string DATE_TIME_FORMAT = 'Y-m-d H:i';
    private const string DEFAULT_MODEL = 'gpt-5-mini';
    private const string DEFAULT_CACHE_DIR = '.cache/infosheet';
    private const string CACHE_RESULTS_DIR = 'results';
    private const string CACHE_MANIFEST_FILENAME = 'manifest.json';
    private const int CACHE_FORMAT_VERSION = 1;
    private const string CACHE_PARSER_VERSION = 'v1';
    private const int DEFAULT_LAST_MODIFIED_STALE_DAYS = 21;

    private string $openAiApiKey;
    private string $model;
    private string $cacheDir;
    private int $lastModifiedStaleDays;

    public function __construct(
        private Client $httpClient,
        private IFSCScheduleFactory $scheduleFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
        $this->openAiApiKey = $this->readEnvironmentVariable('OPENAI_API_KEY');
        $model = $this->readEnvironmentVariable('OPENAI_MODEL');
        $this->model = $model !== '' ? $model : self::DEFAULT_MODEL;

        $cacheDir = $this->readEnvironmentVariable('IFSC_INFOSHEET_CACHE_DIR');
        $cacheDir = rtrim($cacheDir !== '' ? $cacheDir : self::DEFAULT_CACHE_DIR, '/');
        $this->cacheDir = $cacheDir !== '' ? $cacheDir : self::DEFAULT_CACHE_DIR;

        $staleDays = $this->readEnvironmentVariable('IFSC_INFOSHEET_CACHE_LAST_MODIFIED_DAYS');
        $this->lastModifiedStaleDays = ctype_digit($staleDays)
            ? max(1, (int) $staleDays)
            : self::DEFAULT_LAST_MODIFIED_STALE_DAYS;
    }

    /**
     * @param array<array<string>> $infoSheetHeaders
     * @return IFSCSchedule[]
     * @throws InfoSheetChatGptScheduleParserException
     */
    public function parseScheduleFromPdf(
        IFSCEventInfo $event,
        string $pdfPath,
        string $infoSheetUrl = '',
        array $infoSheetHeaders = [],
    ): array
    {
        $normalizedHeaders = $this->normalizeHeaders($infoSheetHeaders);
        $pdfHash = $this->hashFile($pdfPath);

        $cachedSchedules = $this->loadCachedScheduleFromIdentifiers(
            event: $event,
            cacheIds: $this->cacheIdsFromHeadersAndHash($normalizedHeaders, $pdfHash),
        );

        if ($cachedSchedules !== null) {
            return $cachedSchedules;
        }

        if ($this->openAiApiKey === '') {
            throw new InfoSheetChatGptScheduleParserException(
                'Missing OPENAI_API_KEY. Set a valid OpenAI API key with available quota.'
            );
        }

        $this->emitChatGptApiCalledEvent($event);
        $fileId = $this->uploadInfoSheet($pdfPath);

        try {
            $rounds = $this->extractSchedulePayload($event, $fileId);

            $this->storeScheduleInCache(
                infoSheetUrl: $infoSheetUrl,
                infoSheetHeaders: $normalizedHeaders,
                pdfHash: $pdfHash,
                rounds: $rounds,
            );

            return $this->hydrateSchedules($rounds, $event->timeZone);
        } finally {
            $this->deleteUploadedFile($fileId);
        }
    }

    /**
     * @param array<array<string>> $infoSheetHeaders
     * @return IFSCSchedule[]|null
     */
    public function loadCachedSchedule(
        IFSCEventInfo $event,
        string $infoSheetUrl,
        array $infoSheetHeaders = [],
    ): ?array {
        $normalizedHeaders = $this->normalizeHeaders($infoSheetHeaders);

        $schedules = $this->loadCachedScheduleFromIdentifiers(
            event: $event,
            cacheIds: $this->cacheIdsFromHeadersAndHash($normalizedHeaders, null),
        );

        if ($schedules !== null) {
            return $schedules;
        }

        if (!$this->isLastModifiedStale($normalizedHeaders)) {
            return null;
        }

        $urlEntry = $this->readUrlCacheEntry($infoSheetUrl);

        if ($urlEntry === null) {
            return null;
        }

        $cacheId = $this->toStringOrNull($urlEntry['cache_id'] ?? null);

        if ($cacheId === null) {
            return null;
        }

        return $this->loadCachedScheduleFromIdentifiers(
            event: $event,
            cacheIds: [$cacheId],
        );
    }

    /**
     * @param string[] $cacheIds
     * @return IFSCSchedule[]|null
     */
    private function loadCachedScheduleFromIdentifiers(IFSCEventInfo $event, array $cacheIds): ?array
    {
        foreach (array_values(array_unique($cacheIds)) as $cacheId) {
            $rounds = $this->readRoundsFromCache($cacheId);

            if ($rounds !== null) {
                $this->emitCacheUsedEvent($event);
                return $this->hydrateSchedules($rounds, $event->timeZone);
            }
        }

        return null;
    }

    /**
     * @param array<string,string> $infoSheetHeaders
     * @param array<array{name:mixed,starts_at:mixed,ends_at:mixed}> $rounds
     */
    private function storeScheduleInCache(
        string $infoSheetUrl,
        array $infoSheetHeaders,
        ?string $pdfHash,
        array $rounds,
    ): void {
        try {
            $cacheIds = $this->cacheIdsFromHeadersAndHash($infoSheetHeaders, $pdfHash);

            if ($cacheIds === [] || !$this->ensureCacheDirectories()) {
                return;
            }

            $rounds = $this->normalizeRoundsForCache($rounds);
            $updatedAt = (new DateTimeImmutable('now'))->format(DateTimeInterface::ATOM);

            $payload = [
                'format_version' => self::CACHE_FORMAT_VERSION,
                'parser_cache_version' => self::CACHE_PARSER_VERSION,
                'model' => $this->model,
                'updated_at' => $updatedAt,
                'rounds' => $rounds,
            ];

            foreach ($cacheIds as $cacheId) {
                $this->writeJsonFile($this->cacheResultFilePath($cacheId), $payload);
            }

            $url = trim($infoSheetUrl);

            if ($url === '') {
                return;
            }

            $manifest = $this->readManifest();
            $urls = $manifest['urls'] ?? [];

            if (!is_array($urls)) {
                $urls = [];
            }

            $urls[hash('sha256', $url)] = [
                'url' => $url,
                'cache_id' => $cacheIds[0],
                'etag' => $this->toStringOrNull($infoSheetHeaders['etag'] ?? null),
                'last_modified' => $this->toStringOrNull($infoSheetHeaders['last-modified'] ?? null),
                'content_length' => $this->toStringOrNull($infoSheetHeaders['content-length'] ?? null),
                'pdf_sha256' => $pdfHash,
                'updated_at' => $updatedAt,
            ];

            $manifest['format_version'] = self::CACHE_FORMAT_VERSION;
            $manifest['urls'] = $urls;

            $this->writeJsonFile($this->manifestFilePath(), $manifest);
        } catch (Throwable) {
        }
    }

    /**
     * @param array<string,string> $infoSheetHeaders
     * @return string[]
     */
    private function cacheIdsFromHeadersAndHash(array $infoSheetHeaders, ?string $pdfHash): array
    {
        $cacheIds = [];

        $etag = $this->toStringOrNull($infoSheetHeaders['etag'] ?? null);

        if ($etag !== null) {
            $cacheIds[] = $this->cacheId('etag', $etag);
        }

        $lastModified = $this->toStringOrNull($infoSheetHeaders['last-modified'] ?? null);
        $contentLength = $this->toStringOrNull($infoSheetHeaders['content-length'] ?? null);

        if ($lastModified !== null || $contentLength !== null) {
            $cacheIds[] = $this->cacheId(
                'meta',
                sprintf('%s|%s', $lastModified ?? '', $contentLength ?? ''),
            );
        }

        if ($pdfHash !== null) {
            $cacheIds[] = $this->cacheId('pdf', $pdfHash);
        }

        return array_values(array_unique($cacheIds));
    }

    /**
     * @param array<mixed> $headers
     * @return array<string,string>
     */
    private function normalizeHeaders(array $headers): array
    {
        /** @var array<string,string> $normalized */
        $normalized = [];

        foreach ($headers as $name => $values) {
            if (!is_string($name) || trim($name) === '') {
                continue;
            }

            $headerName = strtolower(trim($name));

            foreach ((array) $values as $value) {
                if (!is_scalar($value) || trim((string) $value) === '') {
                    continue;
                }

                $normalized[$headerName] = trim((string) $value);
                break;
            }
        }

        return $normalized;
    }

    /** @return array<string,mixed>|null */
    private function readUrlCacheEntry(string $infoSheetUrl): ?array
    {
        try {
            $url = trim($infoSheetUrl);

            if ($url === '') {
                return null;
            }

            $manifest = $this->readManifest();
            $urls = $manifest['urls'] ?? null;

            if (!is_array($urls)) {
                return null;
            }

            $entry = $urls[hash('sha256', $url)] ?? null;

            return is_array($entry) ? $entry : null;
        } catch (Throwable) {
            return null;
        }
    }

    /** @param array<string,string> $infoSheetHeaders */
    private function isLastModifiedStale(array $infoSheetHeaders): bool
    {
        $lastModified = $this->toStringOrNull($infoSheetHeaders['last-modified'] ?? null);

        if ($lastModified === null) {
            return false;
        }

        $timestamp = strtotime($lastModified);

        if ($timestamp === false) {
            return false;
        }

        return $timestamp <= (time() - ($this->lastModifiedStaleDays * 86400));
    }

    /** @return array<array{name:mixed,starts_at:mixed,ends_at:mixed}>|null */
    private function readRoundsFromCache(string $cacheId): ?array
    {
        try {
            $path = $this->cacheResultFilePath($cacheId);

            if (!is_file($path)) {
                return null;
            }

            $json = @file_get_contents($path);

            if (!is_string($json) || trim($json) === '') {
                return null;
            }

            $payload = $this->decodeJson($json);

            if (($payload['format_version'] ?? null) !== self::CACHE_FORMAT_VERSION ||
                ($payload['parser_cache_version'] ?? null) !== self::CACHE_PARSER_VERSION
            ) {
                return null;
            }

            if (!array_key_exists('rounds', $payload)) {
                return null;
            }

            $rounds = $payload['rounds'];

            if (!is_array($rounds)) {
                return null;
            }

            return $this->normalizeRoundsForCache($rounds);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param mixed $rounds
     * @return array<array{name:mixed,starts_at:mixed,ends_at:mixed}>
     */
    private function normalizeRoundsForCache(mixed $rounds): array
    {
        if (!is_array($rounds)) {
            return [];
        }

        $normalized = [];

        foreach ($rounds as $round) {
            if (!is_array($round)) {
                continue;
            }

            $name = $this->toStringOrNull($round['name'] ?? null);
            $startsAt = $this->toStringOrNull($round['starts_at'] ?? null);
            $endsAt = $round['ends_at'] ?? null;

            if ($name === null || $startsAt === null) {
                continue;
            }

            $normalized[] = [
                'name' => $name,
                'starts_at' => $startsAt,
                'ends_at' => is_string($endsAt) && trim($endsAt) !== '' ? trim($endsAt) : null,
            ];
        }

        return $normalized;
    }

    private function cacheId(string $kind, string $value): string
    {
        return hash('sha256', "{$kind}|{$value}");
    }

    private function hashFile(string $pdfPath): ?string
    {
        $hash = @hash_file('sha256', $pdfPath);

        if (!is_string($hash) || trim($hash) === '') {
            return null;
        }

        return $hash;
    }

    private function ensureCacheDirectories(): bool
    {
        return $this->ensureDirectory($this->cacheDir) &&
            $this->ensureDirectory($this->cacheResultsDirectoryPath());
    }

    private function ensureDirectory(string $directory): bool
    {
        if (is_dir($directory)) {
            return true;
        }

        return @mkdir($directory, 0777, true) || is_dir($directory);
    }

    private function cacheResultFilePath(string $cacheId): string
    {
        return sprintf('%s/%s.json', $this->cacheResultsDirectoryPath(), $cacheId);
    }

    private function cacheResultsDirectoryPath(): string
    {
        return sprintf('%s/%s', $this->cacheDir, self::CACHE_RESULTS_DIR);
    }

    private function manifestFilePath(): string
    {
        return sprintf('%s/%s', $this->cacheDir, self::CACHE_MANIFEST_FILENAME);
    }

    /** @return array<string,mixed> */
    private function readManifest(): array
    {
        $path = $this->manifestFilePath();

        if (!is_file($path)) {
            return $this->emptyManifest();
        }

        $json = @file_get_contents($path);

        if (!is_string($json) || trim($json) === '') {
            return $this->emptyManifest();
        }

        try {
            $manifest = $this->decodeJson($json);
        } catch (InfoSheetChatGptScheduleParserException) {
            return $this->emptyManifest();
        }

        $urls = $manifest['urls'] ?? null;

        if (!is_array($urls)) {
            $urls = [];
        }

        return [
            'format_version' => self::CACHE_FORMAT_VERSION,
            'urls' => $urls,
        ];
    }

    /** @return array<string,mixed> */
    private function emptyManifest(): array
    {
        return [
            'format_version' => self::CACHE_FORMAT_VERSION,
            'urls' => [],
        ];
    }

    /** @param array<string,mixed> $payload */
    private function writeJsonFile(string $path, array $payload): void
    {
        try {
            $json = json_encode($payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            @file_put_contents($path, "{$json}\n", LOCK_EX);
        } catch (JsonException) {
        }
    }

    /**
     * @param array<array{name:mixed,starts_at:mixed,ends_at:mixed}> $rounds
     * @return IFSCSchedule[]
     */
    private function hydrateSchedules(array $rounds, DateTimeZone $timeZone): array
    {
        /** @var IFSCSchedule[] $schedules */
        $schedules = [];
        /** @var array<string,bool> $seen */
        $seen = [];

        foreach ($rounds as $round) {
            $name = $this->toStringOrNull($round['name'] ?? null);
            $startsAt = $this->parseDateTime($round['starts_at'] ?? null, $timeZone);

            if ($name === null || $startsAt === null) {
                continue;
            }

            $schedule = $this->scheduleFactory->create(
                name: $name,
                startsAt: $startsAt,
                endsAt: $this->parseDateTime($round['ends_at'] ?? null, $timeZone),
            );

            if ($schedule->isPreRound) {
                continue;
            }

            $id = sprintf(
                '%s|%s|%s',
                strtolower($schedule->name),
                $schedule->startsAt->format(DateTimeInterface::RFC3339),
                $schedule->endsAt?->format(DateTimeInterface::RFC3339) ?? '',
            );

            if (isset($seen[$id])) {
                continue;
            }

            $seen[$id] = true;
            $schedules[] = $schedule;
        }

        return $schedules;
    }

    /** @return array<array{name:mixed,starts_at:mixed,ends_at:mixed}> */
    private function extractSchedulePayload(IFSCEventInfo $event, string $fileId): array
    {
        try {
            $response = $this->httpClient->request(
                method: 'POST',
                uri: self::OPENAI_RESPONSES_URL,
                options: [
                    RequestOptions::HEADERS => $this->jsonHeaders(),
                    RequestOptions::JSON => [
                        'model' => $this->model,
                        'input' => [[
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'input_text',
                                    'text' => $this->buildPrompt($event),
                                ],
                                [
                                    'type' => 'input_file',
                                    'file_id' => $fileId,
                                ],
                            ],
                        ]],
                        'text' => [
                            'format' => [
                                'type' => 'json_schema',
                                'name' => 'ifsc_infosheet_schedule',
                                'schema' => $this->responseSchema(),
                                'strict' => true,
                            ],
                        ],
                    ],
                ],
            );
        } catch (GuzzleException $e) {
            throw new InfoSheetChatGptScheduleParserException(
                $this->buildOpenAiExceptionMessage(
                    operation: 'Unable to parse infosheet with ChatGPT',
                    exception: $e,
                ),
                previous: $e,
            );
        }

        $payload = $this->decodeJson((string) $response->getBody());

        $schedule = $this->extractOutputJson($payload);

        if ($schedule === null) {
            $content = $payload['output_text'] ?? $this->extractOutputText($payload);

            if (!is_string($content) || trim($content) === '') {
                throw new InfoSheetChatGptScheduleParserException('ChatGPT returned an empty schedule response');
            }

            $schedule = $this->decodeJson($content);
        }
        $rounds = $schedule['rounds'] ?? null;

        if (!is_array($rounds)) {
            throw new InfoSheetChatGptScheduleParserException('ChatGPT schedule payload is missing rounds');
        }

        /** @var array<array{name:mixed,starts_at:mixed,ends_at:mixed}> $rounds */
        return $rounds;
    }

    /** @throws InfoSheetChatGptScheduleParserException */
    private function uploadInfoSheet(string $pdfPath): string
    {
        $stream = fopen($pdfPath, 'rb');

        if ($stream === false) {
            throw new InfoSheetChatGptScheduleParserException('Unable to open infosheet PDF');
        }

        try {
            try {
                $response = $this->httpClient->request(
                    method: 'POST',
                    uri: self::OPENAI_FILES_URL,
                    options: [
                        RequestOptions::HEADERS => $this->authHeaders(),
                        RequestOptions::MULTIPART => [
                            [
                                'name' => 'purpose',
                                'contents' => self::OPENAI_FILE_PURPOSE,
                            ],
                            [
                                'name' => 'file',
                                'contents' => $stream,
                                'filename' => $this->asPdfFilename($pdfPath),
                                'headers' => ['Content-Type' => 'application/pdf'],
                            ],
                        ],
                    ],
                );
            } catch (GuzzleException $e) {
                throw new InfoSheetChatGptScheduleParserException(
                    $this->buildOpenAiExceptionMessage(
                        operation: 'Unable to upload infosheet PDF',
                        exception: $e,
                    ),
                    previous: $e,
                );
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        $payload = $this->decodeJson((string) $response->getBody());
        $fileId = $payload['id'] ?? null;

        if (!is_string($fileId) || trim($fileId) === '') {
            throw new InfoSheetChatGptScheduleParserException('OpenAI did not return a file id');
        }

        return $fileId;
    }

    private function deleteUploadedFile(string $fileId): void
    {
        try {
            $this->httpClient->request(
                method: 'DELETE',
                uri: sprintf(self::OPENAI_FILE_DELETE_URL, $fileId),
                options: [
                    RequestOptions::HEADERS => $this->authHeaders(),
                ],
            );
        } catch (GuzzleException) {
        }
    }

    private function parseDateTime(mixed $value, DateTimeZone $timeZone): ?DateTimeImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        $fromFormat = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $value, $timeZone);

        if ($fromFormat instanceof DateTimeImmutable) {
            return $fromFormat;
        }

        try {
            return (new DateTimeImmutable($value, $timeZone))->setTimezone($timeZone);
        } catch (Exception) {
            return null;
        }
    }

    private function toStringOrNull(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array<string,mixed>
     * @throws InfoSheetChatGptScheduleParserException
     */
    private function decodeJson(string $json): array
    {
        try {
            $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InfoSheetChatGptScheduleParserException(
                "Unable to parse ChatGPT JSON response: {$e->getMessage()}",
                previous: $e,
            );
        }

        if (!is_array($decoded)) {
            throw new InfoSheetChatGptScheduleParserException('ChatGPT response is not a JSON object');
        }

        return $decoded;
    }

    /** @param array<string,mixed> $response */
    private function extractOutputText(array $response): ?string
    {
        $output = $response['output'] ?? null;

        if (!is_array($output)) {
            return null;
        }

        foreach ($output as $item) {
            if (!is_array($item)) {
                continue;
            }

            $content = $item['content'] ?? null;

            if (!is_array($content)) {
                continue;
            }

            foreach ($content as $contentItem) {
                if (!is_array($contentItem)) {
                    continue;
                }

                $text = $contentItem['text'] ?? null;

                if (is_string($text) && trim($text) !== '') {
                    return $text;
                }
            }
        }

        return null;
    }

    /**
     * @param array<string,mixed> $response
     * @return array<string,mixed>|null
     */
    private function extractOutputJson(array $response): ?array
    {
        $output = $response['output'] ?? null;

        if (!is_array($output)) {
            return null;
        }

        foreach ($output as $item) {
            if (!is_array($item)) {
                continue;
            }

            $content = $item['content'] ?? null;

            if (!is_array($content)) {
                continue;
            }

            foreach ($content as $contentItem) {
                if (!is_array($contentItem)) {
                    continue;
                }

                $json = $contentItem['json'] ?? null;

                if (is_array($json)) {
                    return $json;
                }
            }
        }

        return null;
    }

    /** @return array<string,mixed> */
    private function responseSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'rounds' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'starts_at' => ['type' => 'string'],
                            'ends_at' => [
                                'type' => ['string', 'null'],
                            ],
                        ],
                        'required' => ['name', 'starts_at', 'ends_at'],
                    ],
                ],
            ],
            'required' => ['rounds'],
        ];
    }

    private function buildPrompt(IFSCEventInfo $event): string
    {
        return sprintf(
            <<<PROMPT
            Parse the attached IFSC infosheet PDF and extract the competition round schedule.

            Event context:
            - Event: %s
            - Local date range: %s to %s
            - Timezone: %s

            Output rules:
            - Return only official competition rounds (Qualification, Semi-Final, Final, etc.).
            - Exclude non-round activities (registration, technical meeting, training, practice, warm-up, isolation opening/closing, ceremony).
            - Keep round names close to the infosheet wording.
            - Every row must include starts_at.
            - Use local venue time in timezone %s.
            - Use YYYY-MM-DD HH:MM format for starts_at and ends_at.
            - Set ends_at to null when no end time is provided.
            PROMPT,
            $event->eventName,
            $event->localStartDate,
            $event->localEndDate,
            $event->timeZone->getName(),
            $event->timeZone->getName(),
        );
    }

    /** @return array<string,string> */
    private function authHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->openAiApiKey}",
        ];
    }

    /** @return array<string,string> */
    private function jsonHeaders(): array
    {
        return [
            ...$this->authHeaders(),
            'Content-Type' => 'application/json',
        ];
    }

    private function readEnvironmentVariable(string $name): string
    {
        $value = $_ENV[$name] ?? getenv($name);

        return is_string($value) ? trim($value) : '';
    }

    private function asPdfFilename(string $pdfPath): string
    {
        $filename = basename($pdfPath);

        if (str_ends_with(strtolower($filename), '.pdf')) {
            return $filename;
        }

        return "{$filename}.pdf";
    }

    private function buildOpenAiExceptionMessage(string $operation, GuzzleException $exception): string
    {
        if (!$exception instanceof RequestException) {
            return "{$operation}: {$exception->getMessage()}";
        }

        $statusCode = $exception->getResponse()?->getStatusCode();
        $apiError = $this->extractOpenAiErrorMessage($exception);

        if ($statusCode === 429 && $this->isQuotaError($apiError)) {
            return "{$operation}: OpenAI quota exceeded (HTTP 429). Check billing/project quota or use an API key with available quota.";
        }

        if ($statusCode !== null && $apiError !== null) {
            return "{$operation}: HTTP {$statusCode} - {$apiError}";
        }

        if ($statusCode !== null) {
            return "{$operation}: HTTP {$statusCode} - {$exception->getMessage()}";
        }

        return "{$operation}: {$exception->getMessage()}";
    }

    private function extractOpenAiErrorMessage(RequestException $exception): ?string
    {
        $response = $exception->getResponse();

        if ($response === null) {
            return null;
        }

        try {
            $payload = $this->decodeJson((string) $response->getBody());
        } catch (InfoSheetChatGptScheduleParserException) {
            return null;
        }

        $error = $payload['error'] ?? null;

        if (!is_array($error)) {
            return null;
        }

        $message = $error['message'] ?? null;

        return is_string($message) && trim($message) !== '' ? trim($message) : null;
    }

    private function isQuotaError(?string $message): bool
    {
        if ($message === null) {
            return false;
        }

        $message = strtolower($message);

        return str_contains($message, 'insufficient_quota')
            || str_contains($message, 'exceeded your current quota')
            || str_contains($message, 'billing');
    }

    private function emitChatGptApiCalledEvent(IFSCEventInfo $event): void
    {
        $this->eventDispatcher->dispatch(new InfoSheetChatGptApiCalledEvent($event->eventName));
    }

    private function emitCacheUsedEvent(IFSCEventInfo $event): void
    {
        $this->eventDispatcher->dispatch(new InfoSheetCacheUsedEvent($event->eventName));
    }
}
