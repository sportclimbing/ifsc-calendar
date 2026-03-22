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
            @unlink($pdfPath);
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

        $parser = new InfoSheetChatGptScheduleParser(
            httpClient: $this->createClient([]),
            scheduleFactory: $this->createScheduleFactory(),
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
            @unlink($pdfPath);
        }
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

    private function toJson(array $payload): string
    {
        try {
            return json_encode($payload, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->fail($e->getMessage());
        }
    }
}
