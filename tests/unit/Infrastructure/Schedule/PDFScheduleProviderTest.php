<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\tests\Infrastructure\Schedule;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\HTMLNormalizer;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetScheduleParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PDFScheduleProviderTest extends TestCase
{
    private readonly InfoSheetScheduleParser $scheduleParser;

    #[Test] public function keqiao_schedule_is_found(): void
    {
        $schedule = $this->parseScheduleFromFile('Keqiao.pdf.html', 'Asia/Shanghai');

        $this->assertCount(6, $schedule);

        $this->assertSame("Women's Boulder Qualification", $schedule[0]->name);
        $this->assertSameDate("2025-04-17T09:00:00+08:00", $schedule[0]->startsAt);
        $this->assertNull($schedule[0]->endsAt);

        $this->assertSame("Men's Boulder Qualification", $schedule[1]->name);
        $this->assertSameDate("2025-04-17T16:00:00+08:00", $schedule[1]->startsAt);
        $this->assertNull($schedule[1]->endsAt);

        $this->assertSame("Women's Boulder Semi-Final", $schedule[2]->name);
        $this->assertSameDate("2025-04-17T12:00:00+08:00", $schedule[2]->startsAt);
        $this->assertNull($schedule[2]->endsAt);

        $this->assertSame("Women's Boulder Final", $schedule[3]->name);
        $this->assertSameDate("2025-04-17T19:00:00+08:00", $schedule[3]->startsAt);
        $this->assertNull($schedule[3]->endsAt);

        $this->assertSame("Men's Boulder Semi-Final", $schedule[4]->name);
        $this->assertSameDate("2025-04-17T12:00:00+08:00", $schedule[4]->startsAt);
        $this->assertNull($schedule[4]->endsAt);

        $this->assertSame("Men's Boulder Final", $schedule[5]->name);
        $this->assertSameDate("2025-04-17T19:00:00+08:00", $schedule[5]->startsAt);
        $this->assertNull($schedule[5]->endsAt);
    }

    #[Test] public function wujiang_schedule_is_found(): void
    {
        $schedule = $this->parseScheduleFromFile('Wujiang.pdf.html', 'Asia/Shanghai');

        $this->assertCount(5, $schedule);

        $this->assertSame("Men's & Women's Lead Qualification", $schedule[0]->name);
        $this->assertSameDate("2025-04-25T09:00:00+08:00", $schedule[0]->startsAt);
        $this->assertSameDate("2025-04-25T15:00:00+08:00", $schedule[0]->endsAt);

        $this->assertSame("Men's & Women's Speed Qualification", $schedule[1]->name);
        $this->assertSameDate("2025-04-25T19:00:00+08:00", $schedule[1]->startsAt);
        $this->assertNull($schedule[1]->endsAt);

        $this->assertSame("Men's & Women's Lead Semi-Final", $schedule[2]->name);
        $this->assertSameDate("2025-04-26T15:00:00+08:00", $schedule[2]->startsAt);
        $this->assertNull($schedule[2]->endsAt);

        $this->assertSame("Men's & Women's Speed Final", $schedule[3]->name);
        $this->assertSameDate("2025-04-26T19:30:00+08:00", $schedule[3]->startsAt);
        $this->assertNull($schedule[3]->endsAt);

        $this->assertSame("Men's & Women's Lead Final", $schedule[4]->name);
        $this->assertSameDate("2025-04-27T19:00:00+08:00", $schedule[4]->startsAt);
        $this->assertNull($schedule[4]->endsAt);
    }

    #[Test] public function bali_schedule_is_found(): void
    {
        $schedule = $this->parseScheduleFromFile('Bali.pdf.html', 'Asia/Makassar');

        $this->assertCount(7, $schedule);

        $this->assertSame("Men's & Women's Lead Qualification", $schedule[0]->name);
        $this->assertSameDate("2025-05-02T14:00:00+08:00", $schedule[0]->startsAt);
        $this->assertSameDate("2025-05-02T19:00:00+08:00", $schedule[0]->endsAt);

        $this->assertSame("Women's Speed Qualification", $schedule[1]->name);
        $this->assertSameDate("2025-05-03T10:58:00+08:00", $schedule[1]->startsAt);
        $this->assertNull($schedule[1]->endsAt);

        $this->assertSame("Men's Speed Qualification", $schedule[2]->name);
        $this->assertSameDate("2025-05-03T11:45:00+08:00", $schedule[2]->startsAt);
        $this->assertSameDate("2025-05-03T12:57:00+08:00", $schedule[2]->endsAt);

        $this->assertSame("Men's & Women's Speed Final", $schedule[3]->name);
        $this->assertSameDate("2025-05-03T15:00:00+08:00", $schedule[3]->startsAt);
        $this->assertSameDate("2025-05-03T16:00:00+08:00", $schedule[3]->endsAt);

        $this->assertSame("Men's & Women's Lead Semi-Final", $schedule[4]->name);
        $this->assertSameDate("2025-05-04T10:00:00+08:00", $schedule[4]->startsAt);
        $this->assertSameDate("2025-05-04T12:30:00+08:00", $schedule[4]->endsAt);

        $this->assertSame("Men's Lead Final", $schedule[5]->name);
        $this->assertSameDate("2025-05-04T20:30:00+08:00", $schedule[5]->startsAt);
        $this->assertNull($schedule[5]->endsAt);

        $this->assertSame("Women's Lead Final", $schedule[6]->name);
        $this->assertSameDate("2025-05-04T21:25:00+08:00", $schedule[6]->startsAt);
        $this->assertNull($schedule[6]->endsAt);
    }

    #[Test] public function curitiba_schedule_is_found(): void
    {
        $schedule = $this->parseScheduleFromFile('Curitiba.pdf.html', 'America/Sao_Paulo');

        $this->assertCount(6, $schedule);

        $this->assertSame("Men's Boulder Qualification", $schedule[0]->name);
        $this->assertSameDate("2025-05-16T09:00:00-03:00", $schedule[0]->startsAt);
        $this->assertSameDate("2025-05-16T13:30:00-03:00", $schedule[0]->endsAt);

        $this->assertSame("Women's Boulder Qualification", $schedule[1]->name);
        $this->assertSameDate("2025-05-16T16:00:00-03:00", $schedule[1]->startsAt);
        $this->assertSameDate("2025-05-16T21:30:00-03:00", $schedule[1]->endsAt);

        $this->assertSame("Men's Boulder Semi-Final", $schedule[2]->name);
        $this->assertSameDate("2025-05-17T10:00:00-03:00", $schedule[2]->startsAt);
        $this->assertSameDate("2025-05-17T12:30:00-03:00", $schedule[2]->endsAt);

        $this->assertSame("Men's Boulder Final", $schedule[3]->name);
        $this->assertSameDate("2025-05-17T17:00:00-03:00", $schedule[3]->startsAt);
        $this->assertNull($schedule[3]->endsAt);

        $this->assertSame("Women's Boulder Semi-Final", $schedule[4]->name);
        $this->assertSameDate("2025-05-18T10:00:00-03:00", $schedule[4]->startsAt);
        $this->assertSameDate("2025-05-18T12:30:00-03:00", $schedule[4]->endsAt);

        $this->assertSame("Women's Boulder Final", $schedule[5]->name);
        $this->assertSameDate("2025-05-18T17:00:00-03:00", $schedule[5]->startsAt);
        $this->assertNull($schedule[5]->endsAt);
    }

    #[Test] public function slc_schedule_is_found(): void
    {
        $schedule = $this->parseScheduleFromFile('Slc.pdf.html', 'America/Denver');

        $this->assertCount(0, $schedule);
    }

    #[Test] public function slc2_schedule_is_found(): void
    {
        $schedule = $this->parseScheduleFromFile('Slc2.pdf.html', 'America/Denver');

        $this->assertCount(6, $schedule);

        $this->assertSame("Women's Boulder Qualification", $schedule[0]->name);
        $this->assertSameDate("2025-05-23T09:00:00-06:00", $schedule[0]->startsAt);
        $this->assertSameDate("2025-05-23T13:30:00-06:00", $schedule[0]->endsAt);

        $this->assertSame("Men's Boulder Qualification", $schedule[1]->name);
        $this->assertSameDate("2025-05-23T15:30:00-06:00", $schedule[1]->startsAt);
        $this->assertSameDate("2025-05-23T21:00:00-06:00", $schedule[1]->endsAt);

        $this->assertSame("Women's Boulder Semi-Final", $schedule[2]->name);
        $this->assertSameDate("2025-05-24T10:00:00-06:00", $schedule[2]->startsAt);
        $this->assertSameDate("2025-05-24T12:30:00-06:00", $schedule[2]->endsAt);

        $this->assertSame("Women's Boulder Final", $schedule[3]->name);
        $this->assertSameDate("2025-05-24T17:00:00-06:00", $schedule[3]->startsAt);
        $this->assertNull($schedule[3]->endsAt);

        $this->assertSame("Men's Boulder Semi-Final", $schedule[4]->name);
        $this->assertSameDate("2025-05-25T10:00:00-06:00", $schedule[4]->startsAt);
        $this->assertSameDate("2025-05-25T12:30:00-06:00", $schedule[4]->endsAt);

        $this->assertSame("Men's Boulder Final", $schedule[5]->name);
        $this->assertSameDate("2025-05-25T17:00:00-06:00", $schedule[5]->startsAt);
        $this->assertNull($schedule[5]->endsAt);
    }

    private function assertSameDate(string $expected, DateTimeImmutable $actual): void
    {
        $this->assertSame($expected, $actual->format(\DateTimeInterface::RFC3339));
    }

    /** @return IFSCSchedule[] */
    private function parseScheduleFromFile(string $filename, string $timeZone): array
    {
        try {
            return $this->scheduleParser->parseSchedule(
                $this->loadTestFile($filename),
                new DateTimeZone($timeZone),
            );
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    private function loadTestFile(string $filename): string
    {
        return file_get_contents(__DIR__ . "/../../../resources/infosheets/{$filename}");
    }

    protected function setUp(): void
    {
        $this->scheduleParser = new InfoSheetScheduleParser(
            new HTMLNormalizer(),
            new IFSCScheduleFactory(
                new IFSCTagsParser(),
                new IFSCRoundNameNormalizer(),
            ),
        );
    }
}
