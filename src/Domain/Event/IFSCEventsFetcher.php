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
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\EventScrapingStartedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventRound;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundsScraper;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;
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
                posterUrl: $scrapedRounds->posterUrl,
            );
        }

        return $events;
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws Exception
     */
    private function fetchScrapedRounds(IFSCEventInfo $event): IFSCScrapedEventsResult
    {
        return $this->roundsScraper->fetchRoundsAndPosterForEvent($event);
    }

    private function generateRounds(IFSCEventInfo $event): array
    {
        $rounds = [];

        foreach ($event->categories as $category) {
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

    /**
     * @return IFSCEventInfo[]
     * @throws IFSCApiClientException
     */
    private function fetchEventsForLeagues(IFSCSeasonYear $season, array $selectedLeagues): array
    {
        return $this->eventInfoProvider->fetchEventsForLeagues(
            leagues: $this->fetchLeaguesForSeason($season, $selectedLeagues),
        );
    }

    private function emitScrapingStartedEvent(IFSCEventInfo $event): void
    {
        $this->eventDispatcher->dispatch(new EventScrapingStartedEvent($event->eventName));
    }

    private function estimatedLocalStartDate(IFSCEventInfo $event): DateTimeImmutable
    {
        return $this->createLocalDate("{$event->localStartDate} 08:00", $event->timeZone);
    }

    private function createLocalDate(string $date, string $timeZone): DateTimeImmutable
    {
        return (new DateTimeImmutable($date))->setTimezone(new DateTimeZone($timeZone));
    }
}
