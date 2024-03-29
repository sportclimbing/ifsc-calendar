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
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class IFSCApiEventInfoProvider implements IFSCEventInfoProviderInterface
{
    private const string IFSC_EVENT_API_ENDPOINT = 'https://ifsc.results.info/api/v1/events/%d';

    private const string IFSC_LEAGUE_API_ENDPOINT = 'https://ifsc.results.info/api/v1/season_leagues/%d';

    private const string IFSC_SEASON_INFO_API_URL = 'https://ifsc.results.info/api/v1/seasons/36';

    public function __construct(
        private IFSCApiClient $apiClient,
        private SerializerInterface $serializer,
    ) {
    }

    /** @inheritdoc */
    #[Override] public function fetchEventInfo(int $eventId): IFSCEventInfo
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
            timeZone: $this->fixTimeZone($response),
            location: $this->fixFatFinger($response->location),
            country: $response->country,
            disciplines: $this->getDisciplines($response->disciplines),
            categories: $categories,
        );
    }

    /** @inheritdoc */
    #[Override] public function fetchEventsForLeagues(array $leagues): array
    {
        $events = [];

        foreach ($leagues as $league) {
            try {
                $response = $this->apiClient->authenticatedGet(
                    sprintf(self::IFSC_LEAGUE_API_ENDPOINT, $league->id)
                );

                foreach ($response->events as $event) {
                    $event->league_name = $league->name;
                }

                $events = array_merge($events, $response->events);
            } catch (HttpException $e) {
                throw new IFSCApiClientException(
                    "Unable to retrieve events for season: {$e->getMessage()}"
                );
            }
        }

        return $events;
    }

    /** @inheritdoc */
    #[Override] public function fetchSeasons(): array
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                self::IFSC_SEASON_INFO_API_URL
            );
        } catch (HttpException $e) {
            throw new IFSCApiClientException(
                "Unable to retrieve events for league: {$e->getMessage()}"
            );
        }

        $seasons = [];

        foreach ($response->leagues as $league) {
            $seasons[$response->name] = new IFSCSeason(
                name: $league->name,
                leagues: $this->buildLeagues($response),
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
                id: $this->parseLeagueId($league),
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

    private function parseLeagueId(object $league): int
    {
        return (int) pathinfo($league->url, PATHINFO_FILENAME);
    }

    private function fixFatFinger(string $location): string
    {
        return str_replace('CIty', 'City', $location);
    }

    private function fixTimeZone(object $response): string
    {
        // ffs ifsc
        return match ($response->location) {
            'Innsbruck' => 'Europe/Vienna',
            'Koper' => 'Europe/Ljubljana',
            default => $response->timezone->value,
        };
    }
}
