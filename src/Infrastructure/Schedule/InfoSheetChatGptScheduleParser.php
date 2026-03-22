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
use GuzzleHttp\RequestOptions;
use JsonException;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;

final readonly class InfoSheetChatGptScheduleParser
{
    private const string OPENAI_FILES_URL = 'https://api.openai.com/v1/files';
    private const string OPENAI_RESPONSES_URL = 'https://api.openai.com/v1/responses';
    private const string OPENAI_FILE_DELETE_URL = 'https://api.openai.com/v1/files/%s';
    private const string OPENAI_FILE_PURPOSE = 'user_data';
    private const string DATE_TIME_FORMAT = 'Y-m-d H:i';
    private const string DEFAULT_MODEL = 'gpt-4.1';

    private string $openAiApiKey;
    private string $model;

    public function __construct(
        private Client $httpClient,
        private IFSCScheduleFactory $scheduleFactory,
    ) {
        $this->openAiApiKey = $this->readEnvironmentVariable('OPENAI_API_KEY');
        $model = $this->readEnvironmentVariable('OPENAI_MODEL');
        $this->model = $model !== '' ? $model : self::DEFAULT_MODEL;
    }

    /**
     * @return IFSCSchedule[]
     * @throws InfoSheetChatGptScheduleParserException
     */
    public function parseScheduleFromPdf(IFSCEventInfo $event, string $pdfPath): array
    {
        if ($this->openAiApiKey === '') {
            throw new InfoSheetChatGptScheduleParserException('Missing OPENAI_API_KEY');
        }

        $fileId = $this->uploadInfoSheet($pdfPath);

        try {
            $rounds = $this->extractSchedulePayload($event, $fileId);

            return $this->hydrateSchedules($rounds, $event->timeZone);
        } finally {
            $this->deleteUploadedFile($fileId);
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
                "Unable to parse infosheet with ChatGPT: {$e->getMessage()}",
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
                                'filename' => basename($pdfPath),
                                'headers' => ['Content-Type' => 'application/pdf'],
                            ],
                        ],
                    ],
                );
            } catch (GuzzleException $e) {
                throw new InfoSheetChatGptScheduleParserException(
                    "Unable to upload infosheet PDF: {$e->getMessage()}",
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
}
