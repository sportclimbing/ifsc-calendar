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
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
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
        private string $siteUrl,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     * @throws IFSCStartListException
     * @throws IFSCApiClientException
     */
    #[Override]
    public function fetchEventsForLeague(IFSCSeasonYear $season, int $leagueId): array
    {
        $events = [];

        foreach ($this->eventInfoProvider->fetchEventsForLeague($leagueId) as $event) {
            $scrapedRounds = $this->fetchScrapedRounds($season, $event);
            $eventInfo = $this->eventInfoProvider->fetchInfo($event->event_id);

            if (!empty($scrapedRounds->rounds)) {
                $rounds = $scrapedRounds->rounds;
            } else {
                $rounds = $this->generateRounds($eventInfo, $scrapedRounds);
            }

            $events[] = new IFSCEvent(
                season: $season,
                eventId: $event->event_id,
                leagueId: $leagueId,
                leagueName: $this->fetchLeagueName($eventInfo),
                timeZone: $event->timezone->value,
                eventName: $event->event,
                location: $this->fixFatFinger($eventInfo->location),
                country: $eventInfo->country,
                poster: $scrapedRounds->poster,
                siteUrl: $this->getSiteUrl($season, $event),
                startsAt: $this->formatDate($scrapedRounds->startDate),
                endsAt: $this->formatDate($scrapedRounds->endDate),
                disciplines: $this->getDisciplines($eventInfo),
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
    private function fetchScrapedRounds(IFSCSeasonYear $season, object $event): IFSCScrapedEventsResult
    {
        return $this->roundsScraper->fetchRoundsAndPosterForEvent(
            season: $season,
            eventId: $event->event_id,
            timeZone: new DateTimeZone($event->timezone->value),
        );
    }

    private function getDisciplines(object $info): array
    {
        $disciplines = [];

        foreach ($info->disciplines as $discipline) {
            if ($discipline->kind === IFSCDiscipline::COMBINED->value) {
                $disciplines[] = IFSCDiscipline::BOULDER->value;
                $disciplines[] = IFSCDiscipline::LEAD->value;
            } else {
                $disciplines = array_merge($disciplines, explode('&', $discipline->kind));
            }
        }

        return array_unique($disciplines);
    }

    private function generateRounds(object $eventInfo, IFSCScrapedEventsResult $scrapedRounds): array
    {
        $rounds = [];

        foreach ($eventInfo->d_cats as $category) {
            foreach ($category->category_rounds as $round) {
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

    private function getSiteUrl(IFSCSeasonYear $season, object $event): string
    {
        $params = [
            'season' => $season->value,
            'event_id' => $event->event_id,
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

    private function getRoundName(object $round): string
    {
        $kind = preg_replace_callback(
            pattern: '~(\w)&(\w)~',
            callback: static fn (array $match): string => $match[1] . ' & ' . $match[2],
            subject: $round->kind,
        );

        return ucwords(sprintf("%s's %s %s", $round->category, $kind, $round->name));
    }

    private function fetchLeagueName(object $eventInfo): string
    {
        return $this->eventInfoProvider->fetchLeagueNameById($eventInfo->league_season_id);
    }

    /**
     * @return IFSCStarter[]
     * @throws IFSCStartListException
     */
    private function buildStartList(int $eventId): array
    {
        return $this->startListGenerator->buildStartList($eventId);
    }

    private function fixFatFinger(string $location): string
    {
        return str_replace('CIty', 'City', $location);
    }
}
