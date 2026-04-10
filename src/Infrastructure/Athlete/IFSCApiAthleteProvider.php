<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\Athlete;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteCupDisciplineRanking;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteCupRanking;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteDisciplinePodium;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteException;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteFederation;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteProviderInterface;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteResult;
use SportClimbing\IfscCalendar\Infrastructure\HttpClient\HttpException;
use SportClimbing\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use SportClimbing\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;

final readonly class IFSCApiAthleteProvider implements IFSCAthleteProviderInterface
{
    private const string IFSC_ATHLETE_API_ENDPOINT = 'https://ifsc.results.info/api/v1/athletes/%d';

    public function __construct(
        private IFSCApiClient $apiClient,
    ) {
    }

    /** @inheritdoc */
    #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_ATHLETE_API_ENDPOINT, $athleteId),
            );
        } catch (HttpException|IFSCApiClientException $e) {
            throw new IFSCAthleteException(
                "Unable to fetch athlete info: {$e->getMessage()}"
            );
        }

        if (!is_object($response)) {
            throw new IFSCAthleteException('Invalid athlete payload received from API');
        }

        return $this->hydrateAthlete($response);
    }

    /** @throws IFSCAthleteException */
    private function hydrateAthlete(object $payload): IFSCAthlete
    {
        return new IFSCAthlete(
            id: $this->requiredInt($payload, 'id'),
            firstName: $this->requiredString($payload, 'firstname'),
            lastName: $this->requiredString($payload, 'lastname'),
            birthday: $this->optionalString($payload, 'birthday'),
            gender: $this->requiredString($payload, 'gender'),
            personalStory: $this->optionalString($payload, 'personal_story'),
            federation: $this->hydrateOptionalFederation(
                $this->optionalObject($payload, 'federation'),
            ),
            country: $this->requiredString($payload, 'country'),
            flagUrl: $this->requiredString($payload, 'flag_url'),
            city: $this->optionalString($payload, 'city'),
            age: $this->optionalInt($payload, 'age'),
            height: $this->optionalInt($payload, 'height'),
            instagram: $this->optionalString($payload, 'instagram'),
            nickname: $this->optionalString($payload, 'nickname'),
            spokenLanguages: $this->optionalString($payload, 'spoken_languages'),
            photoUrl: $this->optionalString($payload, 'photo_url'),
            actionPhotoUrl: $this->optionalString($payload, 'action_photo_url'),
            disciplinePodiums: $this->hydrateDisciplinePodiums(
                $this->requiredArray($payload, 'discipline_podiums'),
                'discipline_podiums',
            ),
            worldChampionshipsDisciplinePodiums: $this->hydrateDisciplinePodiums(
                $this->requiredArray($payload, 'world_championships_discipline_podiums'),
                'world_championships_discipline_podiums',
            ),
            continentalChampionshipsDisciplinePodiums: $this->hydrateDisciplinePodiums(
                $this->requiredArray($payload, 'continental_championships_discipline_podiums'),
                'continental_championships_discipline_podiums',
            ),
            allResults: $this->hydrateResults(
                $this->requiredArray($payload, 'all_results'),
            ),
            cupRankings: $this->hydrateCupRankings(
                $this->requiredArray($payload, 'cup_rankings'),
            ),
        );
    }

    /** @throws IFSCAthleteException */
    private function hydrateOptionalFederation(?object $payload): ?IFSCAthleteFederation
    {
        if ($payload === null) {
            return null;
        }

        return $this->hydrateFederation($payload);
    }

    /** @throws IFSCAthleteException */
    private function hydrateFederation(object $payload): IFSCAthleteFederation
    {
        return new IFSCAthleteFederation(
            id: $this->requiredInt($payload, 'id'),
            name: $this->requiredString($payload, 'name'),
            abbreviation: $this->requiredString($payload, 'abbreviation'),
        );
    }

    /**
     * @param array<mixed> $payload
     * @return IFSCAthleteDisciplinePodium[]
     * @throws IFSCAthleteException
     */
    private function hydrateDisciplinePodiums(array $payload, string $field): array
    {
        $podiums = [];

        foreach ($payload as $index => $podium) {
            if (!is_object($podium)) {
                throw new IFSCAthleteException(sprintf(
                    'Invalid athlete payload: %s[%d] must be an object',
                    $field,
                    $index,
                ));
            }

            $podiums[] = new IFSCAthleteDisciplinePodium(
                disciplineKind: $this->requiredString($podium, 'discipline_kind'),
                total: $this->requiredInt($podium, 'total'),
                firstPlace: $this->requiredInt($podium, '1'),
                secondPlace: $this->requiredInt($podium, '2'),
                thirdPlace: $this->requiredInt($podium, '3'),
            );
        }

        return $podiums;
    }

    /**
     * @param array<mixed> $payload
     * @return IFSCAthleteResult[]
     * @throws IFSCAthleteException
     */
    private function hydrateResults(array $payload): array
    {
        $results = [];

        foreach ($payload as $index => $result) {
            if (!is_object($result)) {
                throw new IFSCAthleteException(sprintf(
                    'Invalid athlete payload: all_results[%d] must be an object',
                    $index,
                ));
            }

            $results[] = new IFSCAthleteResult(
                season: $this->requiredString($result, 'season'),
                rank: $this->requiredInt($result, 'rank'),
                discipline: $this->requiredString($result, 'discipline'),
                eventName: $this->requiredString($result, 'event_name'),
                eventId: $this->requiredInt($result, 'event_id'),
                dCat: $this->requiredInt($result, 'd_cat'),
                date: $this->requiredString($result, 'date'),
                categoryName: $this->requiredString($result, 'category_name'),
                resultUrl: $this->requiredString($result, 'result_url'),
            );
        }

        return $results;
    }

    /**
     * @param array<mixed> $payload
     * @return IFSCAthleteCupRanking[]
     * @throws IFSCAthleteException
     */
    private function hydrateCupRankings(array $payload): array
    {
        $cupRankings = [];

        foreach ($payload as $index => $cupRanking) {
            if (!is_object($cupRanking)) {
                throw new IFSCAthleteException(sprintf(
                    'Invalid athlete payload: cup_rankings[%d] must be an object',
                    $index,
                ));
            }

            $cupRankings[] = new IFSCAthleteCupRanking(
                name: $this->requiredString($cupRanking, 'name'),
                id: $this->requiredInt($cupRanking, 'id'),
                season: $this->requiredString($cupRanking, 'season'),
                lead: $this->hydrateOptionalCupDisciplineRanking(
                    $this->optionalObject($cupRanking, 'lead'),
                ),
                boulder: $this->hydrateOptionalCupDisciplineRanking(
                    $this->optionalObject($cupRanking, 'boulder'),
                ),
            );
        }

        return $cupRankings;
    }

    /** @throws IFSCAthleteException */
    private function hydrateOptionalCupDisciplineRanking(?object $payload): ?IFSCAthleteCupDisciplineRanking
    {
        if ($payload === null) {
            return null;
        }

        return $this->hydrateCupDisciplineRanking($payload);
    }

    /** @throws IFSCAthleteException */
    private function hydrateCupDisciplineRanking(object $payload): IFSCAthleteCupDisciplineRanking
    {
        return new IFSCAthleteCupDisciplineRanking(
            rank: $this->requiredInt($payload, 'rank'),
            resultUrl: $this->requiredString($payload, 'result_url'),
            dCatId: $this->requiredInt($payload, 'd_cat_id'),
            disciplineKindId: $this->requiredInt($payload, 'disc_kind_id'),
        );
    }

    /** @throws IFSCAthleteException */
    private function requiredString(object $payload, string $field): string
    {
        $value = $this->requiredValue($payload, $field);

        if (!is_string($value)) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: %s must be a string',
                $field,
            ));
        }

        return $value;
    }

    /** @throws IFSCAthleteException */
    private function optionalString(object $payload, string $field): ?string
    {
        $value = $this->optionalValue($payload, $field);

        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: %s must be a string',
                $field,
            ));
        }

        return $value;
    }

    /** @throws IFSCAthleteException */
    private function requiredInt(object $payload, string $field): int
    {
        $value = $this->requiredValue($payload, $field);

        if (!is_int($value)) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: %s must be an integer',
                $field,
            ));
        }

        return $value;
    }

    /** @throws IFSCAthleteException */
    private function optionalInt(object $payload, string $field): ?int
    {
        $value = $this->optionalValue($payload, $field);

        if ($value === null) {
            return null;
        }

        if (!is_int($value)) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: %s must be an integer',
                $field,
            ));
        }

        return $value;
    }

    /**
     * @return array<mixed>
     * @throws IFSCAthleteException
     */
    private function requiredArray(object $payload, string $field): array
    {
        $value = $this->requiredValue($payload, $field);

        if (!is_array($value)) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: %s must be an array',
                $field,
            ));
        }

        return $value;
    }

    /** @throws IFSCAthleteException */
    private function requiredObject(object $payload, string $field): object
    {
        $value = $this->requiredValue($payload, $field);

        if (!is_object($value)) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: %s must be an object',
                $field,
            ));
        }

        return $value;
    }

    /** @throws IFSCAthleteException */
    private function optionalObject(object $payload, string $field): ?object
    {
        $value = $this->optionalValue($payload, $field);

        if ($value === null) {
            return null;
        }

        if (!is_object($value)) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: %s must be an object',
                $field,
            ));
        }

        return $value;
    }

    /** @throws IFSCAthleteException */
    private function requiredValue(object $payload, string $field): mixed
    {
        if (!property_exists($payload, $field) || $payload->{$field} === null) {
            throw new IFSCAthleteException(sprintf(
                'Invalid athlete payload: missing non-null field "%s"',
                $field,
            ));
        }

        return $payload->{$field};
    }

    private function optionalValue(object $payload, string $field): mixed
    {
        if (!property_exists($payload, $field)) {
            return null;
        }

        return $payload->{$field};
    }
}
