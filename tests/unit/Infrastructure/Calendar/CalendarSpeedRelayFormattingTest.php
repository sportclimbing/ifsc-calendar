<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Infrastructure\Calendar;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDisciplines;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRound;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundCategory;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundKind;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundStatus;
use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;
use SportClimbing\IfscCalendar\Domain\Stream\LiveStream;
use SportClimbing\IfscCalendar\Infrastructure\Calendar\CalendarFactory;
use SportClimbing\IfscCalendar\Infrastructure\Calendar\ICalCalendar;
use SportClimbing\IfscCalendar\Infrastructure\Calendar\JsonCalendar;

final class CalendarSpeedRelayFormattingTest extends TestCase
{
    #[Test] public function json_calendar_uses_speed_relay_in_round_name_and_speed_in_disciplines(): void
    {
        $calendar = new JsonCalendar();
        $output = $calendar->generateForEvents([$this->createEventWithSpeedRelayRound()]);
        $payload = json_decode($output, associative: true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(['speed'], $payload['events'][0]['disciplines']);
        $this->assertSame("Men's Speed Relay Qualification", $payload['events'][0]['rounds'][0]['name']);
        $this->assertSame(['speed'], $payload['events'][0]['rounds'][0]['disciplines']);
    }

    #[Test] public function ical_calendar_uses_speed_relay_in_summary(): void
    {
        $calendar = new ICalCalendar(
            calendarFactory: new CalendarFactory(),
            productIdentifier: '-//ifsc-calendar//tests//EN',
            publishedTtl: 'PT1H',
            calendarName: 'IFSC Calendar Tests',
        );

        $output = $calendar->generateForEvents([$this->createEventWithSpeedRelayRound()]);

        $this->assertStringContainsString("Men's Speed Relay Qualification - Madrid (ES)", $output);
        $this->assertStringNotContainsString('Speed_Relay', $output);
    }

    private function createEventWithSpeedRelayRound(): IFSCEvent
    {
        $timeZone = new DateTimeZone('Europe/Madrid');
        $roundStart = new DateTimeImmutable('2026-09-01 10:00:00', $timeZone);
        $roundEnd = new DateTimeImmutable('2026-09-01 11:30:00', $timeZone);
        $round = new IFSCRound(
            name: "Men's Speed Relay Qualification",
            categories: [IFSCRoundCategory::MEN],
            disciplines: new IFSCDisciplines([IFSCDiscipline::SPEED_RELAY]),
            kind: IFSCRoundKind::QUALIFICATION,
            liveStream: new LiveStream(url: 'https://youtube.com/watch?v=relay-test'),
            startTime: $roundStart,
            endTime: $roundEnd,
            status: IFSCRoundStatus::CONFIRMED,
        );

        return new IFSCEvent(
            season: IFSCSeasonYear::SEASON_2026,
            eventId: 9999,
            slug: 'ifsc-speed-relay-test-2026',
            leagueName: 'World Cups and World Championships',
            timeZone: $timeZone,
            eventName: 'IFSC Speed Relay Test Event 2026',
            location: 'Madrid',
            country: 'ES',
            siteUrl: 'https://ifsc.stream/season/2026/event/ifsc-speed-relay-test-2026',
            infosheetUrl: null,
            startsAt: $roundStart,
            endsAt: $roundEnd,
            disciplines: [IFSCDiscipline::SPEED_RELAY],
            rounds: [$round],
            startList: [],
            startListTotal: 0,
            ticketsSummary: null,
            ticketsPurchaseUrl: null,
            countryName: 'Spain',
        );
    }
}
