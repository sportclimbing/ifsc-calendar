<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use Closure;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\EventScrapingStartedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventRound;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRankingException;
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
        private string $siteUrl,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     * @throws IFSCStartListException
     * @throws IFSCApiClientException
     * @throws IFSCWorldRankingException
     */
    #[Override]
    public function fetchEventsForSeason(IFSCSeasonYear $season): array
    {
        $events = [];

        foreach ($this->eventInfoProvider->fetchEventsForSeason($season) as $event) {
            $this->emitScrapingStartedEvent($event);
            $eventId = $this->getEventId($event);

            $eventInfo = $this->eventInfoProvider->fetchEventInfo($eventId);
            $scrapedRounds = $this->fetchScrapedRounds($event, $eventInfo);

            if (!empty($scrapedRounds->rounds)) {
                $rounds = $scrapedRounds->rounds;
            } else {
                $rounds = $this->generateRounds($eventInfo, $scrapedRounds);
            }

            $events[] = new IFSCEvent(
                season: $season,
                eventId: $eventId,
                slug: $event->slug,
                leagueName: $this->fetchLeagueName($eventInfo),
                timeZone: $eventInfo->timeZone,
                eventName: $event->title,
                location: $this->fixFatFinger($eventInfo->location),
                country: $eventInfo->country,
                poster: $scrapedRounds->poster,
                siteUrl: $this->getSiteUrl($season, $eventId),
                startsAt: $this->formatDate($scrapedRounds->startDate),
                endsAt: $this->formatDate($scrapedRounds->endDate),
                disciplines: $eventInfo->disciplines,
                rounds: $rounds,
                starters: $this->buildStartList($eventId),
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
            slug: $event->slug,
            timeZone: new DateTimeZone($eventInfo->timeZone),
        );
    }

    private function generateRounds(IFSCEventInfo $eventInfo, IFSCScrapedEventsResult $scrapedRounds): array
    {
        $rounds = [];

        foreach ($eventInfo->categories as $category) {
            foreach ($category->rounds as $round) {
                $rounds[] = $this->roundFactory->create(
                    name: $this->getRoundName($round),
                    streamUrl: new StreamUrl(),
                    startTime: $scrapedRounds->startDate,
                    endTime: $scrapedRounds->startDate->modify('+3 hours'),
                    status: IFSCRoundStatus::ESTIMATED,
                );
            }
        }

        return $rounds;
    }

    private function getSiteUrl(IFSCSeasonYear $season, int $eventId): string
    {
        $params = [
            'season' => $season->value,
            'event_id' => $eventId,
        ];

        return preg_replace_callback('~{(?<var_name>season|event_id)}~', $this->replaceVariables($params), $this->siteUrl);
    }

    private function replaceVariables(array $params): Closure
    {
        return static fn (array $match): string => (string) $params[$match['var_name']];
    }

    public function formatDate(DateTimeImmutable $scrapedRounds): string
    {
        return $scrapedRounds->format('Y-m-d\TH:i:s');
    }

    private function getRoundName(IFSCEventRound $round): string
    {
        $discipline = preg_replace_callback(
            pattern: '~(\w)&(\w)~',
            callback: static fn (array $match): string => $match[1] . ' & ' . $match[2],
            subject: $round->discipline,
        );

        return ucwords(sprintf("%s's %s %s", $round->category, $discipline, $round->kind->value));
    }

    /** @throws IFSCApiClientException */
    private function fetchLeagueName(IFSCEventInfo $eventInfo): string
    {
        return $this->eventInfoProvider->fetchLeagueNameById($eventInfo->leagueSeasonId);
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

    private function fixFatFinger(string $location): string
    {
        return str_replace('CIty', 'City', $location);
    }

    private function getEventId(object $event): int
    {
        return (int) $event->fields->verticalLifeEventId;
    }

    private function emitScrapingStartedEvent(object $event): void
    {
        $this->eventDispatcher->dispatch(new EventScrapingStartedEvent($event->title));
    }
}
