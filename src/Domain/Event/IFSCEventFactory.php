<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use SportClimbing\IfscCalendar\Domain\Calendar\SiteURLBuilder;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRound;
use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListGenerator;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListResult;
use RuntimeException;

final readonly class IFSCEventFactory
{
    public function __construct(
        private SiteURLBuilder $siteURLBuilder,
        private IFSCStartListGenerator $startListGenerator,
    ) {
    }

    /** @param IFSCRound[] $rounds */
    public function create(IFSCSeasonYear $season, IFSCEventInfo $event, array $rounds): IFSCEvent
    {
        [$startDate, $endDate] = $this->generateDateRangeFromRounds($rounds, $event);

        return new IFSCEvent(
            season: $season,
            eventId: $event->eventId,
            slug: $event->slug,
            leagueName: $event->leagueName,
            timeZone: $event->timeZone,
            eventName: $event->eventName,
            location: $event->location,
            country: $event->country,
            siteUrl: $this->siteURLBuilder->build($season, $event),
            infosheetUrl: $event->infosheetUrl,
            startsAt: $startDate,
            endsAt: $endDate,
            disciplines: $event->disciplines,
            rounds: $rounds,
            startList: ($startListResult = $this->buildStartList($event->eventId))->starters,
            startListTotal: $startListResult->total,
            ticketsSummary: $event->ticketsSummary,
            ticketsPurchaseUrl: $event->ticketsPurchaseUrl,
        );
    }

    /**
     * @param IFSCRound[] $rounds
     * @return DateTimeImmutable[]
     */
    private function generateDateRangeFromRounds(array $rounds, IFSCEventInfo $event): array
    {
        $confirmedDates = [];

        foreach ($rounds as $round) {
            if ($round->status->isConfirmed() || $round->status->isProvisional()) {
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
    private function buildStartList(int $eventId): IFSCStartListResult
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

    private function createLocalDate(string $date, DateTimeZone $timeZone): DateTimeImmutable
    {
        return new DateTimeImmutable($date, $timeZone);
    }
}
