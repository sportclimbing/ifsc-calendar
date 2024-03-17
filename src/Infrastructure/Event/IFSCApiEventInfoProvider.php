<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Event;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventInfoProviderInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;

final readonly class IFSCApiEventInfoProvider implements IFSCEventInfoProviderInterface
{
    private const string IFSC_EVENT_API_ENDPOINT = 'https://ifsc.results.info/api/v1/events/%d';

    private const string IFSC_EVENTS_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=%d';

    private const string IFSC_LEAGUE_API_ENDPOINT = 'https://ifsc.results.info/api/v1/season_leagues/%d';

    private const string IFSC_SEASONS_API_URL = 'https://components.ifsc-climbing.org/results-api.php?api=index';

    public function __construct(
        private IFSCApiClient $apiClient,
    ) {
    }

    /** @inheritdoc */
    #[Override]
    public function fetchInfo(int $eventId): object
    {
        try {
            return $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_EVENT_API_ENDPOINT, $eventId),
            );
        } catch (HttpException $e) {
            throw new IFSCApiClientException(
                "Unable to retrieve events info: {$e->getMessage()}"
            );
        }
    }

    /** @inheritdoc */
    #[Override]
    public function fetchEventsForLeague(int $leagueId): array
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_EVENTS_API_ENDPOINT, $leagueId),
            );
        } catch (HttpException $e) {
            throw new IFSCApiClientException(
                "Unable to retrieve events for league: {$e->getMessage()}"
            );
        }

        return $response->events;
    }


    /** @inheritdoc */
    #[Override]
    public function fetchLeagueNameById(int $leagueId): string
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_LEAGUE_API_ENDPOINT, $leagueId),
            );
        } catch (HttpException $e) {
            throw new IFSCApiClientException(
                "Unable to retrieve league name: {$e->getMessage()}"
            );
        }

        return $response->league;
    }

    /** @inheritdoc */
    #[Override]
    public function fetchSeasons(): array
    {
        try {
            $response = $this->apiClient->request(
                self::IFSC_SEASONS_API_URL
            );
        } catch (HttpException $e) {
            throw new IFSCApiClientException(
                "Unable to retrieve events for league: {$e->getMessage()}"
            );
        }

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
