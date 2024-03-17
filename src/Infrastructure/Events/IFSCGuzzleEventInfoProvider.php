<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Events;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventInfoProviderInterface;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use Override;

final readonly class IFSCGuzzleEventInfoProvider implements IFSCEventInfoProviderInterface
{
    private const string IFSC_EVENT_API_ENDPOINT = 'https://ifsc.results.info/api/v1/events/%d';

    private const string IFSC_EVENTS_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=%d';

    private const string IFSC_LEAGUE_API_ENDPOINT = 'https://ifsc.results.info/api/v1/season_leagues/%d';

    private const string IFSC_SEASONS_API_URL = 'https://components.ifsc-climbing.org/results-api.php?api=index';

    public function __construct(
        private IFSCApiClient $apiClient,
    ) {
    }

    #[Override]
    public function fetchInfo(int $eventId): object
    {
        return $this->apiClient->fetchAuth(
            sprintf(self::IFSC_EVENT_API_ENDPOINT, $eventId),
        );
    }

    #[Override]
    public function fetchEventsForLeague(int $leagueId): array
    {
        $response = $this->apiClient->fetchAuth(
            sprintf(self::IFSC_EVENTS_API_ENDPOINT, $leagueId),
        );

        return $response->events;
    }

    #[Override]
    public function fetchLeagueNameById(int $leagueId): string
    {
        $response = $this->apiClient->fetchAuth(
            sprintf(self::IFSC_LEAGUE_API_ENDPOINT, $leagueId),
        );

        return $response->league;
    }

    /** @inheritdoc */
    #[Override]
    public function fetchSeasons(): array
    {
        $response = $this->apiClient->request(
            self::IFSC_SEASONS_API_URL
        );

        $seasons = [];

        foreach ($response->seasons as $season) {
            $seasons[$season->name] = new IFSCSeason(
                id: $season->id,
                name: $season->name,
                leagues: $this->buildLeagues($season),
            );
        }

        return $seasons;
    }

    /** @return IFSCLeague[] */
    private function buildLeagues(object $season): array
    {
        $leagues = [];

        foreach ($season->leagues as $league) {
            $leagues[] = new IFSCLeague(
                id: $league->id,
                name: $league->name,
            );
        }

        return $leagues;
    }
}
