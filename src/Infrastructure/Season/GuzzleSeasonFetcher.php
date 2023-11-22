<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Season;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonFetcherInterface;

final readonly class GuzzleSeasonFetcher implements IFSCSeasonFetcherInterface
{
    private const IFSC_SEASONS_API_URL = 'https://components.ifsc-climbing.org/results-api.php?api=index';

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    /**
     * @return IFSCSeason[]
     * @throws Exception|GuzzleException
     */
    public function fetchSeasons(): array
    {
        $response = $this->client->getRetry(self::IFSC_SEASONS_API_URL);
        $response = @json_decode($response);

        if (json_last_error()) {
            throw new Exception(json_last_error_msg());
        }

        $seasons = [];

        foreach ($response->seasons as $season) {
            $seasons[$season->name] = new IFSCSeason(
                name: $season->name,
                id: $season->id,
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
                name: $league->name,
                id: $league->id,
            );
        }

        return $leagues;
    }
}
