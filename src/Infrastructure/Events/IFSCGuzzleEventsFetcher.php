<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Events;

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundsScraper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Starter\IFSCStarter;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpGuzzleClient;

final readonly class IFSCGuzzleEventsFetcher implements IFSCEventFetcherInterface
{
    private const IFSC_LEAGUE_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=%d';

    private const IFSC_STARTERS_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=starters&event_id=%d';

    private const IFSC_EVENT_API_ENDPOINT = 'https://ifsc.results.info/api/v1/events/%d';

    private const IFSC_SESSION_COOKIE_NAME = '_verticallife_resultservice_session';

    private const IFSC_RESULTS_INFO_PAGE = 'https://ifsc.results.info/';

    public function __construct(
        private IFSCRoundsScraper $roundsScraper,
        private HttpGuzzleClient $httpClient,
        private string $siteUrl,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     * @throws InvalidURLException
     * @throws Exception
     */
    public function fetchEventsForLeague(IFSCSeasonYear $season, int $leagueId): array
    {
        $sessionId = $this->fetchSessionIdCookie();
        $events = [];

        foreach ($this->fetchJsonEventsForLeague($leagueId)->events as $event) {
            $scrapedRounds = $this->fetchScrapedRounds($season, $event);
            $eventInfo = $this->fetchAdditionalEventInfo($event->event_id, $sessionId);

            if (!empty($scrapedRounds->rounds)) {
                $rounds = $scrapedRounds->rounds;
            } else {
                $rounds = $this->generateRounds($eventInfo, $scrapedRounds);
            }

            $events[] = new IFSCEvent(
                season: $season,
                eventId: $event->event_id,
                timeZone: $event->timezone->value,
                eventName: $event->event,
                location: $eventInfo->location,
                country: $eventInfo->country,
                poster: $scrapedRounds->poster,
                siteUrl: $this->getSiteUrl($season, $event),
                startsAt: $this->formatDate($scrapedRounds->startDate),
                endsAt: $this->formatDate($scrapedRounds->endDate),
                disciplines: $this->getDisciplines($eventInfo),
                rounds: $rounds,
                starters: $this->fetchAthletes($event->event_id, $sessionId),
            );
        }

        return $events;
    }

    /** @throws IFSCEventsScraperException */
    private function fetchAdditionalEventInfo(int $eventId, string $sessionId): object
    {
        return $this->fetchAuth($this->buildInfoUri($eventId), $sessionId);
    }

    /** @throws IFSCEventsScraperException */
    private function fetchAuth(string $url, string $sessionId): object|array
    {
        return $this->request($url, [
            RequestOptions::HEADERS => [
                // Apparently, this is required to pass the authorization check
                'referer' => self::IFSC_RESULTS_INFO_PAGE,
            ],
            RequestOptions::COOKIES => CookieJar::fromArray(
                cookies: [self::IFSC_SESSION_COOKIE_NAME => $sessionId],
                domain: 'ifsc.results.info',
            ),
        ]);
    }

    /** @throws IFSCEventsScraperException */
    private function fetchJsonEventsForLeague(int $leagueId): object
    {
        return $this->request($this->buildLeagueUri($leagueId));
    }

    /** @throws IFSCEventsScraperException */
    private function fetchJsonStartersForEvent(int $eventId): array
    {
        return $this->request($this->buildStartersUri($eventId));
    }

    /** @throws IFSCEventsScraperException */
    private function request(string $url, array $options = []): object|array
    {
        try {
            $response = $this->httpClient->getRetry($url, $options);

            return @json_decode($response, flags: JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new IFSCEventsScraperException("Unable to retrieve HTML: {$e->getMessage()}");
        } catch (JsonException $e) {
            throw new IFSCEventsScraperException("Unable to parse JSON: {$e->getMessage()}");
        }
    }

    /** @throws IFSCEventsScraperException */
    private function fetchSessionIdCookie(): string
    {
        try {
            $headers = $this->httpClient->getHeaders(self::IFSC_RESULTS_INFO_PAGE);
        }  catch (GuzzleException $e) {
            throw new IFSCEventsScraperException("Unable to retrieve HTTP headers: {$e->getMessage()}");
        }

        $cookies = $headers['set-cookie'] ?? [];

        foreach ($cookies as $cookie) {
            if (str_starts_with($cookie, self::IFSC_SESSION_COOKIE_NAME)) {
                [ , $value] = explode('=', $cookie, limit: 2);
                [$sessionId] = explode(';', $value, limit: 2);

                return $sessionId;
            }
        }

        throw new IFSCEventsScraperException('Could not retrieve session cookie');
    }

    /**
     * @throws InvalidURLException
     * @throws IFSCEventsScraperException
     */
    private function fetchScrapedRounds(IFSCSeasonYear $season, object $event): IFSCScrapedEventsResult
    {
        return $this->roundsScraper->fetchRoundsAndPosterForEvent(
            season: $season,
            eventId: $event->event_id,
            timeZone: $event->timezone->value,
        );
    }

    /** @throws IFSCEventsScraperException */
    private function fetchStarters(int $eventId): array
    {
        $starters = [];

        foreach ($this->fetchJsonStartersForEvent($eventId) as $starter) {
            $starters[] = [
                'firstName' => $starter->firstname,
                'lastName' => $starter->lastname,
                'country' => $starter->country,
            ];
        }

        return $starters;
    }

    private function getDisciplines(object $info): array
    {
        $disciplines = [];

        foreach ($info->disciplines as $discipline) {
            if ($discipline->kind === 'combined') {
                $disciplines[] = 'boulder';
                $disciplines[] = 'lead';
            } else {
                $disciplines = array_merge($disciplines, explode('&', $discipline->kind));
            }
        }

        return array_unique($disciplines);
    }

    /** @throws Exception */
    private function generateRounds(object $eventInfo, IFSCScrapedEventsResult $scrapedRounds): array
    {
        $rounds = [];

        foreach ($eventInfo->d_cats as $category) {
            foreach ($category->category_rounds as $round) {
                $rounds[] = new IFSCRound(
                    name: $this->getRoundName($round),
                    streamUrl: null,
                    startTime: $scrapedRounds->startDate,
                    endTime: $scrapedRounds->startDate->modify('+3 hours'),
                    scheduleConfirmed: false,
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

    /**
     * @return IFSCStarter[]
     * @throws IFSCEventsScraperException
     */
    private function fetchAthletes(int $eventId, string $sessionId): array
    {
        $curw = $this->fetchAuth('https://ifsc.results.info/api/v1/cuwr', $sessionId);
        $starters = $this->fetchStarters($eventId);
        $athletes = [];
        $scores = [];

        foreach ($curw as $value) {
            $response = $this->fetchAuth("https://ifsc.results.info/api/v1/cuwr/{$value->dcat_id}", $sessionId);

            foreach ($response->ranking as $athlete) {
                if (!isset($scores[$athlete->athlete_id])) {
                    $scores[$athlete->athlete_id] = 0;
                }

                $scores[$athlete->athlete_id] += $athlete->score;

                $athletes[$athlete->athlete_id] = [
                    'id' => $athlete->athlete_id,
                    'firstname' => $athlete->firstname,
                    'lastname' => $athlete->lastname,
                    'country' => $athlete->country,
                    'photo_url' => $athlete->photo_url ?? null,
                ];
            }
        }

        foreach ($scores as $athleteId => $score) {
            $athletes[$athleteId]['score'] = $score;
        }

        usort($athletes, static fn (array $athlete1, array $athlete2): int => $athlete2['score'] <=> $athlete1['score']);
        $matches = [];

        foreach ($starters as $starter) {
            foreach ($athletes as $athlete) {
                if ($this->starterMatchesAthlete($starter, $athlete)) {
                    $matches[] = new IFSCStarter(
                        firstName: $starter['firstName'],
                        lastName: $starter['lastName'],
                        country: $starter['country'],
                        score: $athlete['score'],
                        photoUrl: $athlete['photo_url'],
                    );

                    if (count($matches) === 20) {
                        break 2;
                    }
                }
            }
        }

        usort($matches, $this->sortByScore());

        return $matches;
    }

    private function starterMatchesAthlete(array $starter, array $athlete): bool
    {
        return
            $starter['firstName'] === $athlete['firstname'] &&
            $starter['lastName'] === $athlete['lastname'] &&
            $starter['country'] === $athlete['country'];
    }

    private function buildLeagueUri(int $leagueId): string
    {
        return sprintf(self::IFSC_LEAGUE_API_ENDPOINT, $leagueId);
    }

    private function buildStartersUri(int $eventId): string
    {
        return sprintf(self::IFSC_STARTERS_API_ENDPOINT, $eventId);
    }

    private function buildInfoUri(int $eventId): string
    {
        return sprintf(self::IFSC_EVENT_API_ENDPOINT, $eventId);
    }

    public function formatDate(DateTimeImmutable $scrapedRounds): string
    {
        return $scrapedRounds->format(DateTimeInterface::RFC3339);
    }

    private function getRoundName(object $round): string
    {
        return sprintf("%s's %s %s", $round->category, ucfirst($round->kind), $round->name);
    }

    private function sortByScore(): Closure
    {
        return static fn (IFSCStarter $athlete1, IFSCStarter $athlete2): int => $athlete2->score <=> $athlete1->score;
    }
}
