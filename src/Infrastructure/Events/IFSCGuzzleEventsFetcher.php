<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Events;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventsScraper;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpGuzzleClient;

final readonly class IFSCGuzzleEventsFetcher implements IFSCEventFetcherInterface
{
    private const IFSC_LEAGUE_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=%d';

    public function __construct(
        private IFSCEventsScraper $eventsScraper,
        private HttpGuzzleClient $client,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     */
    public function fetchEventsForLeague(int $season, int $league): array
    {
        $response = $this->fetchHtmlForLeague($league);
        $events = [];

        foreach ($response->events as $event) {
            $scrapedEvents = $this->eventsScraper->fetchEventsForLeague(
                season: $season,
                eventId: $event->event_id,
                timeZone: $event->timezone->value,
                eventName: $event->event,
            );

            if (empty($scrapedEvents)) {
                // throw IFSCEventsScraperException::noEventsScrapedForEventWithName($event->event);
            }

            foreach ($scrapedEvents as $eventDetails) {
                $events[] = $eventDetails;
            }
        }

        return $events;
    }

    public function buildLeagueUri(int $leagueId): string
    {
        return sprintf(self::IFSC_LEAGUE_API_ENDPOINT, $leagueId);
    }

    /** @throws IFSCEventsScraperException */
    public function fetchHtmlForLeague(int $league): object
    {
        try {
            $response = $this->client->getRetry($this->buildLeagueUri($league));

            return @json_decode($response, flags: JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new IFSCEventsScraperException("Unable to retrieve HTML: {$e->getMessage()}");
        } catch (JsonException $e) {
            throw new IFSCEventsScraperException("Unable to parse JSON: {$e->getMessage()}");
        }
    }
}
