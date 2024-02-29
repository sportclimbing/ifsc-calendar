<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Events;

use Closure;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Event\IFSCRoundsScraper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpGuzzleClient;

final readonly class IFSCGuzzleEventsFetcher implements IFSCEventFetcherInterface
{
    private const IFSC_LEAGUE_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=%d';

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
    public function fetchEventsForLeague(int $season, int $leagueId): array
    {
        $sessionId = $this->fetchSessionIdCookie();
        $events = [];

        foreach ($this->fetchJsonEventsForLeague($leagueId)->events as $event) {
            $scrapedRounds = $this->fetchScrapedRounds($season, $event);
            $eventInfo = $this->fetchAdditionalEventInfo($event->event_id, $sessionId);

            if (!empty($scrapedRounds->rounds)) {
                $rounds = $scrapedRounds->rounds;
            } else {
                $rounds = $this->generateRounds($eventInfo);
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
                startsAt: $eventInfo->starts_at,
                endsAt: $eventInfo->ends_at,
                disciplines: $this->fetchDisciplines($eventInfo),
                rounds: $rounds,
            );
        }

        return $events;
    }

    /** @throws IFSCEventsScraperException */
    private function fetchAdditionalEventInfo(int $eventId, string $sessionId): object
    {
        return $this->request($this->buildInfoUri($eventId), [
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
    private function request(string $url, array $options = []): object
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
    private function fetchScrapedRounds(int $season, object $event): IFSCScrapedEventsResult
    {
        return $this->roundsScraper->fetchRoundsAndPosterForEvent(
            season: $season,
            eventId: $event->event_id,
            timeZone: $event->timezone->value,
        );
    }

    private function fetchDisciplines(object $info): array
    {
        $disciplines = [];

        foreach ($info->disciplines as $discipline) {
            $disciplines = array_merge($disciplines, explode('&', $discipline->kind));
        }

        return array_unique($disciplines);
    }

    /** @throws Exception */
    private function generateRounds(object $eventInfo): array
    {
        $rounds = [];

        foreach ($eventInfo->d_cats as $category) {
            foreach ($category->category_rounds as $round) {
                $rounds[] = new IFSCRound(
                    name: sprintf("%s's %s %s", $round->category, ucfirst($round->kind), $round->name),
                    streamUrl: null,
                    startTime: $this->getStartTime($eventInfo),
                    endTime: $this->getStartTime($eventInfo)->modify('+3 hours'),
                    scheduleConfirmed: false,
                );
            }
        }

        return $rounds;
    }

    private function getSiteUrl(int $season, object $event): string
    {
        $params = [
            'season' => $season,
            'event_id' => $event->event_id,
        ];

        return preg_replace_callback('~{(?<var_name>season|event_id)}~', $this->replaceVariables($params), $this->siteUrl);
    }

    private function replaceVariables(array $params): Closure
    {
        return static fn (array $match): string => (string) $params[$match['var_name']];
    }

    private function buildLeagueUri(int $leagueId): string
    {
        return sprintf(self::IFSC_LEAGUE_API_ENDPOINT, $leagueId);
    }

    private function buildInfoUri(int $eventId): string
    {
        return sprintf(self::IFSC_EVENT_API_ENDPOINT, $eventId);
    }

    /** @throws Exception */
    private function getStartTime(object $eventInfo): DateTimeImmutable
    {
        return new DateTimeImmutable($eventInfo->starts_at, new DateTimeZone($eventInfo->timezone->value));
    }
}
