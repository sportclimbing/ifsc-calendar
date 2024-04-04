<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\SiteURLBuilder;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStartListGenerator;
use RuntimeException;

final readonly class IFSCEventFactory
{
    public function __construct(
        private SiteURLBuilder $siteURLBuilder,
        private IFSCEventsSlug $eventsSlug,
        private IFSCStartListGenerator $startListGenerator,
    ) {
    }

    public function create(IFSCSeasonYear $season, IFSCEventInfo $event, array $rounds, ?string $posterUrl): IFSCEvent
    {
        [$startDate, $endDate] = $this->generateDateRangeFromRounds($rounds, $event);

        return new IFSCEvent(
            season: $season,
            eventId: $event->eventId,
            slug: $this->eventsSlug->create($event->eventName),
            leagueName: $event->leagueName,
            timeZone: $event->timeZone,
            eventName: $event->eventName,
            location: $event->location,
            country: $event->country,
            poster: $posterUrl,
            siteUrl: $this->siteURLBuilder->build($season, $event->eventId),
            startsAt: $startDate,
            endsAt: $endDate,
            disciplines: $event->disciplines,
            rounds: $rounds,
            starters: $this->buildStartList($event->eventId),
        );
    }

    /** @param IFSCRound[] $rounds */
    private function generateDateRangeFromRounds(array $rounds, IFSCEventInfo $event): array
    {
        $confirmedDates = [];

        foreach ($rounds as $round) {
            if ($round->status->isConfirmed()) {
                $confirmedDates[] = $round->startTime;
            }
        }

        if (count($confirmedDates) >= 2) {
            return [min($confirmedDates), max($confirmedDates)];
        }

        return [
            $this->estimatedLocalStartDate($event),
            $this->estimatedLocalEndDate($event),
        ];
    }

    /** @throws RuntimeException */
    private function buildStartList(int $eventId): array
    {
        try {
            return $this->startListGenerator->buildStartList($eventId);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    private function estimatedLocalStartDate(IFSCEventInfo $event): DateTimeImmutable
    {
        return $this->createLocalDate("{$event->localStartDate} 08:00", $event->timeZone);
    }

    private function estimatedLocalEndDate(IFSCEventInfo $event): DateTimeImmutable
    {
        return $this->createLocalDate("{$event->localEndDate} 16:00", $event->timeZone);
    }

    private function createLocalDate(string $date, string $timeZone): DateTimeImmutable
    {
        return (new DateTimeImmutable($date))->setTimezone(new DateTimeZone($timeZone));
    }
}
