<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Events;

use GuzzleHttp\Client;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventsScraper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;

final readonly class IFSCGuzzleEventsFetcher implements IFSCEventFetcherInterface
{
    private const IFSC_LEAGUE_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=%d';

    public function __construct(
        private IFSCEventsScraper $eventsScraper,
        private Client $client,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     */
    public function fetchEventsForLeague(int $season, IFSCLeague $league): array
    {
        $response = $this->client->get($this->buildLeagueUri($league->id))->getBody()->getContents();
        $response = @json_decode($response);

        if (json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }

        $events = [];

        foreach ($response->events as $event) {
            $scrapedEvents = $this->eventsScraper->fetchEventsForLeague(
                season: $season,
                eventId: $event->event_id,
                timezone: $event->timezone->value,
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
}
