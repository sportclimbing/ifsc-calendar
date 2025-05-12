<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\Round;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCAverageRoundDuration;
use nicoSWD\IfscCalendar\Domain\Round\IFSCAverageRoundDurationLookupKey;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLiveStreamFinderInterface;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCRoundFactoryTest extends TestCase
{
    #[Test] public function finished_live_stream_start_and_end_time_is_used(): void
    {
        $roundFactory = $this->roundFactoryReturningLiveStreamWith(
            scheduledStartTime: '2024-04-12T10:55:00Z',
            duration: 60,
        );

        $round = $roundFactory->create(
            event: $this->createEvent(),
            roundName: "Men's & Women's Lead Qualification",
            startTime: $this->createDateTime('2024-04-12 18:30'),
            endTime: $this->createDateTime('2024-04-12 21:23'),
            status: IFSCRoundStatus::PROVISIONAL,
        );

        $this->assertSame(IFSCRoundStatus::CONFIRMED, $round->status);
        $this->assertSame('2024-04-12T19:00:00+08:00', $this->formatDate($round->startTime));
        $this->assertSame('2024-04-12T20:00:00+08:00', $this->formatDate($round->endTime));
    }

    #[Test] public function average_round_duration_is_used_when_not_streamed_yet(): void
    {
        $roundFactory = $this->roundFactoryReturningLiveStreamWith(
            scheduledStartTime: '2024-04-12T10:55:00Z',
            duration: 0,
        );

        $round = $roundFactory->create(
            event: $this->createEvent(),
            roundName: "Men's & Women's Lead Qualification",
            startTime: $this->createDateTime('2024-04-12 18:30'),
            endTime: null,
            status: IFSCRoundStatus::PROVISIONAL,
        );

        $this->assertSame(IFSCRoundStatus::CONFIRMED, $round->status);
        $this->assertSame('2024-04-12T19:00:00+08:00', $this->formatDate($round->startTime));
        $this->assertSame('2024-04-12T20:30:00+08:00', $this->formatDate($round->endTime));
    }

    #[Test] public function round_schedule_is_not_confirmed_unless_a_live_stream_was_found(): void
    {
        $roundFactory = $this->roundFactoryReturningLiveStreamWith(
            scheduledStartTime: null,
            duration: 0,
        );

        $round = $roundFactory->create(
            event: $this->createEvent(),
            roundName: "Men's & Women's Lead Qualification",
            startTime: $this->createDateTime('2024-04-12 18:30'),
            endTime: null,
            status: IFSCRoundStatus::PROVISIONAL,
        );

        $this->assertSame(IFSCRoundStatus::PROVISIONAL, $round->status);
        $this->assertSame('2024-04-13T02:30:00+08:00', $this->formatDate($round->startTime));
        $this->assertSame('2024-04-13T04:00:00+08:00', $this->formatDate($round->endTime));
    }

    #[Test] public function end_time_is_guessed_based_on_youtube_if_start_dates_mismatch(): void
    {
        $roundFactory = $this->roundFactoryReturningLiveStreamWith(
            scheduledStartTime: '2024-04-12T17:55:00+08:00',
            duration: 0,
        );

        $round = $roundFactory->create(
            event: $this->createEvent(),
            roundName: "Men's & Women's Lead Qualification",
            startTime: $this->createDateTime('2024-04-12T19:00:00+08:00'),
            endTime: $this->createDateTime('2024-04-12T20:00:00+08:00'),
            status: IFSCRoundStatus::PROVISIONAL,
        );

        $this->assertSame(IFSCRoundStatus::CONFIRMED, $round->status);
        $this->assertSame('2024-04-12T18:00:00+08:00', $this->formatDate($round->startTime));
        $this->assertSame('2024-04-12T19:00:00+08:00', $this->formatDate($round->endTime));
    }

    private function roundFactoryReturningLiveStreamWith(?string $scheduledStartTime, int $duration): IFSCRoundFactory
    {
        return new IFSCRoundFactory(
            new IFSCTagsParser(),
            new readonly class ($scheduledStartTime, $duration) implements YouTubeLiveStreamFinderInterface {
                public function __construct(
                    private ?string $scheduledStartTime,
                    private int $duration,
                ) {
                }

                #[Override] public function findLiveStream(IFSCEventInfo $event, string $roundName): LiveStream
                {
                    return new LiveStream(
                        scheduledStartTime: $this->scheduledStartTime ? new DateTimeImmutable($this->scheduledStartTime) : null,
                        duration: $this->duration,
                    );
                }
            },
            new IFSCAverageRoundDuration(
                new IFSCAverageRoundDurationLookupKey(),
            ),
        );
    }

    private function createEvent(): IFSCEventInfo
    {
        return new IFSCEventInfo(
            eventId: 1292,
            eventName: 'IFSC World Cup Salt Lake City 2024',
            leagueId: 37,
            leagueName: 'World Cups and World Championships',
            leagueSeasonId: 12,
            localStartDate: '2023-04-10T11:55:00Z',
            localEndDate: '2023-04-10T13:55:00Z',
            timeZone: new DateTimeZone('Asia/Shanghai'),
            location: 'Barcelona',
            country: 'ES',
            disciplines: [],
            categories: [],
        );
    }

    private function formatDate(DateTimeImmutable $dateTime): string
    {
        return $dateTime->format(DateTimeInterface::RFC3339);
    }

    private function createDateTime(string $dateTime): DateTimeImmutable
    {
        return new DateTimeImmutable($dateTime)->setTimezone(new DateTimeZone('Asia/Shanghai'));
    }
}
