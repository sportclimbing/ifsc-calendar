<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\SiteURLBuilder;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\EventScrapingStartedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventRound;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRankingException;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundsScraper;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStarter;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStartListException;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStartListGenerator;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;

final readonly class IFSCEventsFetcher implements IFSCEventFetcherInterface
{
    public function __construct(
        private IFSCRoundsScraper $roundsScraper,
        private IFSCStartListGenerator $startListGenerator,
        private IFSCRoundFactory $roundFactory,
        private IFSCEventInfoProviderInterface $eventInfoProvider,
        private EventDispatcherInterface $eventDispatcher,
        private SiteURLBuilder $siteURLBuilder,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     * @throws IFSCStartListException
     * @throws IFSCApiClientException
     * @throws IFSCWorldRankingException
     */
    #[Override] public function fetchEventsForSeason(IFSCSeasonYear $season, array $selectedLeagues): array
    {
        $events = [];

        foreach ($this->fetchEventsForLeagues($season, $selectedLeagues) as $event) {
            $this->emitScrapingStartedEvent($event);

            $eventInfo = $this->eventInfoProvider->fetchEventInfo($event->event_id);
            $scrapedRounds = $this->fetchScrapedRounds($event, $eventInfo);

            if (!empty($scrapedRounds->rounds)) {
                $rounds = $scrapedRounds->rounds;
            } else {
                $rounds = $this->generateRounds($event, $eventInfo);
            }

            [$startDate, $endDate] = $this->generateDateRangeFromRounds($rounds, $event);

            $events[] = new IFSCEvent(
                season: $season,
                eventId: $event->event_id,
                slug: $this->buildSlug($event),
                leagueName: $event->league_name,
                timeZone: $eventInfo->timeZone,
                eventName: $event->event,
                location: $eventInfo->location,
                country: $eventInfo->country,
                poster: $scrapedRounds->poster,
                siteUrl: $this->siteURLBuilder->build($season, $event->event_id),
                startsAt: $this->formatDate($startDate),
                endsAt: $this->formatDate($endDate),
                disciplines: $eventInfo->disciplines,
                rounds: $rounds,
                starters: $this->buildStartList($event->event_id),
            );
        }

        return $events;
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws Exception
     */
    private function fetchScrapedRounds(object $event, IFSCEventInfo $eventInfo): IFSCScrapedEventsResult
    {
        return $this->roundsScraper->fetchRoundsAndPosterForEvent(
            event: $event,
            timeZone: new DateTimeZone($eventInfo->timeZone),
        );
    }

    private function generateRounds(object $event, IFSCEventInfo $eventInfo): array
    {
        $rounds = [];

        foreach ($eventInfo->categories as $category) {
            foreach ($category->rounds as $round) {
                $startTime = $this->estimatedLocalStartDate($event);

                $rounds[] = $this->roundFactory->create(
                    name: $this->normalizeRoundName($round),
                    streamUrl: new StreamUrl(),
                    startTime: $startTime,
                    endTime: $startTime->modify('90 minutes'),
                    status: IFSCRoundStatus::ESTIMATED,
                );
            }
        }

        return $rounds;
    }

    public function formatDate(DateTimeImmutable $scrapedRounds): string
    {
        return $scrapedRounds->format(DateTimeInterface::RFC3339);
    }

    private function normalizeRoundName(IFSCEventRound $round): string
    {
        $discipline = preg_replace_callback(
            pattern: '~(\w)&(\w)~',
            callback: static fn (array $match): string => $match[1] . ' & ' . $match[2],
            subject: $round->discipline,
        );

        return ucwords(sprintf("%s's %s %s", $round->category, $discipline, $round->kind->value));
    }

    /**
     * @return IFSCStarter[]
     * @throws IFSCStartListException
     * @throws IFSCWorldRankingException
     */
    private function buildStartList(int $eventId): array
    {
        return $this->startListGenerator->buildStartList($eventId);
    }

    /**
     * @param IFSCSeasonYear $season
     * @param IFSCLeague[] $selectedLeagues
     * @return IFSCLeague[]
     * @throws IFSCApiClientException
     */
    private function fetchLeaguesForSeason(IFSCSeasonYear $season, array $selectedLeagues): array
    {
        $seasons = $this->eventInfoProvider->fetchSeasons();
        $filteredLeagues = [];

        foreach ($seasons[$season->value]->leagues as $league) {

            if (in_array($league->name, $selectedLeagues, strict: true)) {
                $filteredLeagues[] = $league;
            }
        }

        return $filteredLeagues;
    }

    /** @param IFSCRound[] $rounds */
    private function generateDateRangeFromRounds(array $rounds, object $event): array
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

    /**
     * @return object[]
     * @throws IFSCApiClientException
     */
    private function fetchEventsForLeagues(IFSCSeasonYear $season, array $selectedLeagues): array
    {
        return $this->eventInfoProvider->fetchEventsForLeagues(
            leagues: $this->fetchLeaguesForSeason($season, $selectedLeagues),
        );
    }

    private function buildSlug(object $event): string
    {
        $eventName = $event->event;
        $eventName = mb_convert_encoding($eventName, mb_detect_encoding($eventName, strict: true), 'UTF-8');
        $eventName = strtr($eventName, ['ç' => 'c']);
        $eventName = preg_replace('~\W+~u', '-', mb_strtolower($eventName));

        return $eventName;
    }

    private function emitScrapingStartedEvent(object $event): void
    {
        $this->eventDispatcher->dispatch(new EventScrapingStartedEvent($event->event));
    }

    private function estimatedLocalStartDate(object $event): DateTimeImmutable
    {
        return $this->createLocalDate("{$event->local_start_date} 08:00", $event->timezone->value);
    }

    private function estimatedLocalEndDate(object $event): DateTimeImmutable
    {
        return $this->createLocalDate("{$event->local_end_date} 16:00", $event->timezone->value);
    }

    private function createLocalDate(string $date, string $timeZone): DateTimeImmutable
    {
        return (new DateTimeImmutable($date))->setTimezone(new DateTimeZone($timeZone));
    }
}
