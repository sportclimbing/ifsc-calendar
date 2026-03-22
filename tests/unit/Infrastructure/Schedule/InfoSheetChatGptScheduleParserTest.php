<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\tests\Infrastructure\Schedule;

use DateTimeInterface;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JsonException;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetCacheUsedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetChatGptApiCalledEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetChatGptScheduleParser;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetChatGptScheduleParserException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InfoSheetChatGptScheduleParserTest extends TestCase
{
    #[Test]
    public function schedule_is_parsed_from_chatgpt_response(): void
    {
        $previousApiKey = $this->setEnv('OPENAI_API_KEY', 'test-key');
        $previousModel = $this->setEnv('OPENAI_MODEL', 'gpt-test');
        $cacheDirectory = $this->createTempDirectory();
        $previousCacheDir = $this->setEnv('IFSC_INFOSHEET_CACHE_DIR', $cacheDirectory);

        $responsePayload = $this->toJson([
            'output_text' => $this->toJson([
                'rounds' => [[
                    'name' => "Men's Boulder Qualification",
                    'starts_at' => '2026-04-17 09:00',
                    'ends_at' => null,
                ]],
            ]),
        ]);

        $parser = new InfoSheetChatGptScheduleParser(
            httpClient: $this->createClient([
                new Response(status: 200, body: $this->toJson(['id' => 'file_123'])),
                new Response(status: 200, body: $responsePayload),
                new Response(status: 200, body: $this->toJson(['deleted' => true])),
            ]),
            scheduleFactory: $this->createScheduleFactory(),
            eventDispatcher: $this->expectSingleDispatchedEvent(InfoSheetChatGptApiCalledEvent::class),
        );

        $pdfPath = $this->createTempPdf();

        try {
            $schedules = $parser->parseScheduleFromPdf(
                event: $this->createEventInfo(),
                pdfPath: $pdfPath,
            );
        } finally {
            $this->restoreEnv('OPENAI_API_KEY', $previousApiKey);
            $this->restoreEnv('OPENAI_MODEL', $previousModel);
            $this->restoreEnv('IFSC_INFOSHEET_CACHE_DIR', $previousCacheDir);
            @unlink($pdfPath);
            $this->removeDirectory($cacheDirectory);
        }

        $this->assertCount(1, $schedules);
        $this->assertSame("Men's Boulder Qualification", $schedules[0]->name);
        $this->assertSame('2026-04-17T09:00:00+02:00', $schedules[0]->startsAt->format(DateTimeInterface::RFC3339));
        $this->assertNull($schedules[0]->endsAt);
    }

    #[Test]
    public function missing_api_key_throws_exception(): void
    {
        $previousApiKey = $this->setEnv('OPENAI_API_KEY', null);
        $previousModel = $this->setEnv('OPENAI_MODEL', null);
        $cacheDirectory = $this->createTempDirectory();
        $previousCacheDir = $this->setEnv('IFSC_INFOSHEET_CACHE_DIR', $cacheDirectory);

        $parser = new InfoSheetChatGptScheduleParser(
            httpClient: $this->createClient([]),
            scheduleFactory: $this->createScheduleFactory(),
            eventDispatcher: $this->expectNoDispatchedEvents(),
        );

        $pdfPath = $this->createTempPdf();
        $this->expectException(InfoSheetChatGptScheduleParserException::class);

        try {
            $parser->parseScheduleFromPdf(
                event: $this->createEventInfo(),
                pdfPath: $pdfPath,
            );
        } finally {
            $this->restoreEnv('OPENAI_API_KEY', $previousApiKey);
            $this->restoreEnv('OPENAI_MODEL', $previousModel);
            $this->restoreEnv('IFSC_INFOSHEET_CACHE_DIR', $previousCacheDir);
            @unlink($pdfPath);
            $this->removeDirectory($cacheDirectory);
        }
    }

    #[Test]
    public function cached_schedule_is_used_when_etag_matches(): void
    {
        $cacheDirectory = $this->createTempDirectory();
        $previousCacheDir = $this->setEnv('IFSC_INFOSHEET_CACHE_DIR', $cacheDirectory);
        $previousModel = $this->setEnv('OPENAI_MODEL', 'gpt-test');
        $previousApiKey = $this->setEnv('OPENAI_API_KEY', 'test-key');

        $responsePayload = $this->toJson([
            'output_text' => $this->toJson([
                'rounds' => [[
                    'name' => "Women's Lead Final",
                    'starts_at' => '2026-07-03 18:00',
                    'ends_at' => null,
                ]],
            ]),
        ]);

        $pdfPath1 = $this->createTempPdf();
        $pdfPath2 = $this->createTempPdf();
        $secondRun = [];

        try {
            $parserThatCallsApi = new InfoSheetChatGptScheduleParser(
                httpClient: $this->createClient([
                    new Response(status: 200, body: $this->toJson(['id' => 'file_123'])),
                    new Response(status: 200, body: $responsePayload),
                    new Response(status: 200, body: $this->toJson(['deleted' => true])),
                ]),
                scheduleFactory: $this->createScheduleFactory(),
                eventDispatcher: $this->expectSingleDispatchedEvent(InfoSheetChatGptApiCalledEvent::class),
            );

            $firstRun = $parserThatCallsApi->parseScheduleFromPdf(
                event: $this->createEventInfo(),
                pdfPath: $pdfPath1,
                infoSheetUrl: 'https://example.test/infosheet.pdf',
                infoSheetHeaders: ['ETag' => ['"etag-123"']],
            );

            $this->assertCount(1, $firstRun);

            $this->restoreEnv('OPENAI_API_KEY', $previousApiKey);
            $previousApiKey = $this->setEnv('OPENAI_API_KEY', null);

            $parserThatMustUseCache = new InfoSheetChatGptScheduleParser(
                httpClient: $this->createClient([]),
                scheduleFactory: $this->createScheduleFactory(),
                eventDispatcher: $this->expectSingleDispatchedEvent(InfoSheetCacheUsedEvent::class),
            );

            $secondRun = $parserThatMustUseCache->parseScheduleFromPdf(
                event: $this->createEventInfo(),
                pdfPath: $pdfPath2,
                infoSheetUrl: 'https://example.test/infosheet.pdf',
                infoSheetHeaders: ['ETag' => ['"etag-123"']],
            );
        } finally {
            $this->restoreEnv('OPENAI_API_KEY', $previousApiKey);
            $this->restoreEnv('OPENAI_MODEL', $previousModel);
            $this->restoreEnv('IFSC_INFOSHEET_CACHE_DIR', $previousCacheDir);
            @unlink($pdfPath1);
            @unlink($pdfPath2);
            $this->removeDirectory($cacheDirectory);
        }

        $this->assertCount(1, $secondRun);
        $this->assertSame("Women's Lead Final", $secondRun[0]->name);
        $this->assertSame('2026-07-03T18:00:00+02:00', $secondRun[0]->startsAt->format(DateTimeInterface::RFC3339));
    }

    /** @param Response[] $responses */
    private function createClient(array $responses): Client
    {
        $mock = new MockHandler($responses);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    private function createScheduleFactory(): IFSCScheduleFactory
    {
        return new IFSCScheduleFactory(
            tagsParser: new IFSCTagsParser(),
            roundNameNormalizer: new IFSCRoundNameNormalizer(),
        );
    }

    private function createEventInfo(): IFSCEventInfo
    {
        return new IFSCEventInfo(
            eventId: 1,
            eventName: 'Test Event',
            leagueId: 1,
            leagueName: 'World Cup',
            leagueSeasonId: 1,
            localStartDate: '2026-04-17',
            localEndDate: '2026-04-18',
            timeZone: new DateTimeZone('Europe/Madrid'),
            location: 'Madrid',
            country: 'Spain',
            disciplines: [],
            categories: [],
        );
    }

    private function createTempPdf(): string
    {
        $tmpFile = tempnam('/tmp', 'infosheet_test_');

        if ($tmpFile === false) {
            $this->fail('Unable to create temporary test file');
        }

        file_put_contents($tmpFile, '%PDF-1.4');

        return $tmpFile;
    }

    private function createTempDirectory(): string
    {
        $tmpDir = tempnam('/tmp', 'infosheet_cache_');

        if ($tmpDir === false) {
            $this->fail('Unable to create temporary directory');
        }

        @unlink($tmpDir);
        mkdir($tmpDir, 0777, true);

        return $tmpDir;
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = scandir($directory);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = "{$directory}/{$item}";

            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($directory);
    }

    private function setEnv(string $name, ?string $value): string|false
    {
        $previousValue = getenv($name);

        if ($value === null) {
            putenv($name);
            unset($_ENV[$name]);

            return $previousValue;
        }

        putenv("{$name}={$value}");
        $_ENV[$name] = $value;

        return $previousValue;
    }

    private function restoreEnv(string $name, string|false $value): void
    {
        if ($value === false) {
            putenv($name);
            unset($_ENV[$name]);

            return;
        }

        putenv("{$name}={$value}");
        $_ENV[$name] = $value;
    }

    private function expectSingleDispatchedEvent(string $eventClass): EventDispatcherInterface
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(static fn (object $event): bool => $event instanceof $eventClass));

        return $eventDispatcher;
    }

    private function expectNoDispatchedEvents(): EventDispatcherInterface
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::never())->method('dispatch');

        return $eventDispatcher;
    }

    /** @param array<string,mixed> $payload */
    private function toJson(array $payload): string
    {
        try {
            return json_encode($payload, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->fail($e->getMessage());
        }
    }
}
