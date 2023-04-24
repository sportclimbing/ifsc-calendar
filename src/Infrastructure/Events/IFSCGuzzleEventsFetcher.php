<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Events;

use GuzzleHttp\Client;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;

final readonly class IFSCGuzzleEventsFetcher implements IFSCEventFetcherInterface
{
    public function __construct(
        private IFSCGuzzleEventsScraper $eventsScraper,
        private Client $client,
    ) {
    }

    /** @inheritdoc */
    public function fetchEventsForLeague(int $season, IFSCLeague $league): array
    {
        $response = $this->client->request('GET', $this->buildLeagueUri($league->id))->getBody()->getContents();
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

            foreach ($scrapedEvents as $eventDetails) {
                $events[] = $eventDetails;
            }
        }

        return $events;
    }

    public function buildLeagueUri(int $id): string
    {
        return sprintf('https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=%d', $id);
    }
}
