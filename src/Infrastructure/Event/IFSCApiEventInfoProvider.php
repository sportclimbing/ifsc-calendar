<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Event;

use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventInfoProviderInterface;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventCategory;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventRound;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundKind;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class IFSCApiEventInfoProvider implements IFSCEventInfoProviderInterface
{
    private const string IFSC_EVENT_API_ENDPOINT = 'https://ifsc.results.info/api/v1/events/%d';

    private const string IFSC_EVENTS_API_ENDPOINT = 'https://www.ifsc-climbing.org/api/dapi/events/all?dateFrom=$range(%d-01-01,%d-12-31)&$limit=100';

    private const string IFSC_LEAGUE_API_ENDPOINT = 'https://ifsc.results.info/api/v1/season_leagues/%d';

    private const string IFSC_SEASONS_API_URL = 'https://components.ifsc-climbing.org/results-api.php?api=index';

    public function __construct(
        private IFSCApiClient $apiClient,
        private SerializerInterface $serializer,
    ) {
    }

    /** @inheritdoc */
    #[Override]
    public function fetchEventInfo(int $eventId): IFSCEventInfo
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_EVENT_API_ENDPOINT, $eventId),
            );
        } catch (HttpException $e) {
            throw new IFSCApiClientException(
                "Unable to retrieve events info: {$e->getMessage()}"
            );
        }

        //return $this->serializer->deserialize($response, IFSCEventInfo::class, 'json');

        $categories = [];

        foreach ($response->d_cats as $category) {
            $rounds = [];

            foreach ($category->category_rounds as $round) {
                $normalizedRoundName = strtolower(
                    str_replace(' ', '-', $round->name)
                );

                $rounds[] = new IFSCEventRound(
                    discipline: $round->kind,
                    kind: IFSCRoundKind::from($normalizedRoundName),
                    category: $round->category,
                );
            }

            $categories[] = new IFSCEventCategory($rounds);
        }

        return new IFSCEventInfo(
            eventId: $response->id,
            eventName: $response->name,
            leagueId: $response->league_id,
            leagueSeasonId: $response->league_season_id,
            timeZone: $response->timezone->value,
            location: $response->location,
            country: $response->country,
            disciplines: $this->getDisciplines($response->disciplines),
            categories: $categories,
        );
    }

    /** @inheritdoc */
    #[Override]
    public function fetchEventsForSeason(IFSCSeasonYear $season): array
    {
        try {
            $response = $this->apiClient->request(
                sprintf(self::IFSC_EVENTS_API_ENDPOINT, $season->value, $season->value)
            );
        } catch (HttpException $e) {
            throw new IFSCApiClientException(
                "Unable to retrieve events for season: {$e->getMessage()}"
            );
        }

        return $response->items;
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

    /** @return IFSCDiscipline[] */
    private function getDisciplines(array $disciplines): array
    {
        $parsedDisciplines = [];

        foreach ($disciplines as $discipline) {
            if ($discipline === IFSCDiscipline::COMBINED->value) {
                $parsedDisciplines[] = IFSCDiscipline::BOULDER->value;
                $parsedDisciplines[] = IFSCDiscipline::LEAD->value;
            } else {
                foreach (explode('&', $discipline->kind) as $kind) {
                    $parsedDisciplines[] = IFSCDiscipline::from($kind)->value;
                }
            }
        }

        return array_map(
            static fn (string $discipline): IFSCDiscipline => IFSCDiscipline::from($discipline),
            array_unique($parsedDisciplines),
        );
    }
}
