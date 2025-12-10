<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Generator;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\EventScrapingStartedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventRound;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundsScraper;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;

final readonly class IFSCEventsFetcher implements IFSCEventFetcherInterface
{
    public function __construct(
        private IFSCRoundsScraper $roundsScraper,
        private IFSCEventFactory $eventFactory,
        private IFSCRoundFactory $roundFactory,
        private IFSCEventInfoProviderInterface $eventInfoProvider,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     * @throws IFSCApiClientException
     * @throws DateMalformedStringException
     */
    #[Override] public function fetchEventsForSeason(IFSCSeasonYear $season, array $selectedLeagues): array
    {
        $events = [];

        foreach ($this->fetchEventsForLeagues($season, $selectedLeagues) as $event) {
            $this->emitScrapingStartedEvent($event);
            $scrapedRounds = $this->fetchScrapedRounds($event);

            if (!empty($scrapedRounds->rounds)) {
                $rounds = $scrapedRounds->rounds;
            } else {
                $rounds = $this->generateRounds($event);
            }

            $events[] = $this->eventFactory->create(
                season: $season,
                event: $event,
                rounds: $rounds,
                posterUrl: null,
            );
        }

        return $events;
    }

    /**
     * @return IFSCRound[]
     * @throws DateMalformedStringException
     */
    private function generateRounds(IFSCEventInfo $event): array
    {
        $rounds = [];

        foreach ($event->categories as $category) {
            foreach ($category->rounds as $round) {
                $startTime = $this->estimatedLocalStartTime($event);

                $rounds[] = $this->roundFactory->create(
                    event: $event,
                    roundName: $this->normalizeRoundName($round),
                    startTime: $startTime,
                    endTime: $startTime->modify('+90 minutes'),
                    status: IFSCRoundStatus::ESTIMATED,
                );
            }
        }

        return $rounds;
    }

    private function normalizeRoundName(IFSCEventRound $round): string
    {
        $discipline = preg_replace_callback(
            pattern: '~(\w)&(\w)~',
            callback: static fn (array $match): string => $match[1] . ' & ' . $match[2],
            subject: $round->discipline,
        );

        return sprintf("%s's %s %s", $round->category, $discipline, $round->kind->value) |> ucwords(...);
    }

    /**
     * @param IFSCSeasonYear $season
     * @param string[] $selectedLeagues
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

    /**
     * @throws IFSCEventsScraperException
     * @throws Exception
     */
    private function fetchScrapedRounds(IFSCEventInfo $event): IFSCScrapedEventsResult
    {
        return $this->roundsScraper->fetchRoundsForEvent($event);
    }

    /**
     * @param string[] $selectedLeagues
     * @return Generator<IFSCEventInfo>
     * @throws IFSCApiClientException
     */
    private function fetchEventsForLeagues(IFSCSeasonYear $season, array $selectedLeagues): Generator
    {
        return $this->eventInfoProvider->fetchEventsForLeagues(
            leagues: $this->fetchLeaguesForSeason($season, $selectedLeagues),
        );
    }

    private function emitScrapingStartedEvent(IFSCEventInfo $event): void
    {
        $this->eventDispatcher->dispatch(new EventScrapingStartedEvent($event->eventName));
    }

    private function estimatedLocalStartTime(IFSCEventInfo $event): DateTimeImmutable
    {
        return $this->createLocalDate("{$event->localStartDate} 08:00", $event->timeZone);
    }

    private function createLocalDate(string $date, DateTimeZone $timeZone): DateTimeImmutable
    {
        return new DateTimeImmutable($date)->setTimezone($timeZone);
    }
}
