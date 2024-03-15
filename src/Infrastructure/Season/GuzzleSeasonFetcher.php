<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Season;

use JsonException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use Override;

final readonly class GuzzleSeasonFetcher implements IFSCSeasonFetcherInterface
{
    private const string IFSC_SEASONS_API_URL = 'https://components.ifsc-climbing.org/results-api.php?api=index';

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    /** @inheritDoc */
    #[Override]
    public function fetchSeasons(): array
    {
        $seasons = [];

        foreach ($this->fetchSeasonsFromApi()->seasons as $season) {
            $seasons[$season->name] = new IFSCSeason(
                id: $season->id,
                name: $season->name,
                leagues: $this->buildLeagues($season),
            );
        }

        return $seasons;
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws HttpException
     */
    #[Override]
    public function fetchLeagueNameById(IFSCSeasonYear $season, int $leagueId): string
    {
        // todo: use https://ifsc.results.info/api/v1/season_leagues/431
        // requires stupid auth token, maybe move to IFSCGuzzleEventsFetcher
        foreach ($this->fetchSeasons()[$season->value]->leagues as $league) {
            if ($league->id === $leagueId) {
                return $league->name;
            }
        }

        throw new IFSCEventsScraperException('Unable to fetch league name');
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

    /** @throws HttpException */
    private function fetchSeasonsFromApi(): object
    {
        try {
            $response = @json_decode($this->getRawJson(), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new HttpException($e->getMessage(), $e->getCode());
        }

        return $response;
    }

    /** @throws HttpException */
    private function getRawJson(): string
    {
        return $this->client->getRetry(self::IFSC_SEASONS_API_URL);
    }
}
