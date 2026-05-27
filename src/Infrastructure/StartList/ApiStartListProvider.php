<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\StartList;

use SportClimbing\IfscCalendar\Domain\StartList\IFSCStarter;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListException;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListProviderInterface;
use SportClimbing\IfscCalendar\Infrastructure\HttpClient\HttpException;
use SportClimbing\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use SportClimbing\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;

final readonly class ApiStartListProvider implements IFSCStartListProviderInterface
{
    private const string IFSC_STARTERS_API_ENDPOINT = 'https://ifsc.results.info/api/v1/events/%d/registrations';

    public function __construct(
        private IFSCApiClient $apiClient,
    ) {
    }

    /** @inheritdoc */
    #[Override] public function fetchStartListForEvent(int $eventId): array
    {
        try {
            $athletes = $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_STARTERS_API_ENDPOINT, $eventId),
            );
        } catch (HttpException|IFSCApiClientException $e) {
            throw new IFSCStartListException(
                "Unable to fetch start list: {$e->getMessage()}"
            );
        }

        $includedAthletes = array_filter(
            $athletes,
            fn (object $athlete): bool => $this->athleteShouldBeIncluded($athlete),
        );

        $starters = [];

        foreach ($includedAthletes as $athlete) {
            foreach ($this->convertToStarterObjects($athlete) as $starter) {
                $starters[$starter->athleteId] = $starter;
            }
        }

        return array_values($starters);
    }

    /** @return IFSCStarter[] */
    private function convertToStarterObjects(object $athlete): array
    {
        $starter = $this->createStarterFromAthletePayload($athlete);

        if ($starter !== null) {
            return [$starter];
        }

        return $this->createStartersFromSquadPayload($athlete);
    }

    private function createStarterFromAthletePayload(object $athlete): ?IFSCStarter
    {
        $athleteId = $this->readInt($athlete->athlete_id ?? null);
        $firstName = $this->readString($athlete->firstname ?? null);
        $lastName = $this->readString($athlete->lastname ?? null);
        $country = $this->readString($athlete->country ?? null);

        if ($athleteId === null || $firstName === null || $lastName === null || $country === null) {
            return null;
        }

        return new IFSCStarter(
            athleteId: $athleteId,
            firstName: $firstName,
            lastName: $this->normalizeLastName($lastName),
            country: $country,
        );
    }

    /** @return IFSCStarter[] */
    private function createStartersFromSquadPayload(object $athlete): array
    {
        if (!isset($athlete->squad_members) || !is_array($athlete->squad_members)) {
            return [];
        }

        $country = $this->readString($athlete->country ?? null);

        if ($country === null) {
            return [];
        }

        $starters = [];

        foreach ($athlete->squad_members as $member) {
            if (!is_object($member)) {
                continue;
            }

            $athleteId = $this->readInt($member->athlete_id ?? null);
            $firstName = $this->readString($member->firstname ?? null);
            $lastName = $this->readString($member->lastname ?? null);

            if ($athleteId === null || $firstName === null || $lastName === null) {
                continue;
            }

            $starters[] = new IFSCStarter(
                athleteId: $athleteId,
                firstName: $firstName,
                lastName: $this->normalizeLastName($lastName),
                country: $country,
            );
        }

        return $starters;
    }

    private function athleteShouldBeIncluded(object $athlete): bool
    {
        if (!isset($athlete->d_cats) || !is_array($athlete->d_cats)) {
            return false;
        }

        $hasExplicitStatus = false;

        foreach ($athlete->d_cats as $category) {
            if (!isset($category->status) || !is_string($category->status)) {
                continue;
            }

            $hasExplicitStatus = true;
            $status = IFSCStartListStatus::tryFrom($category->status);

            if ($this->isAttending($status)) {
                return true;
            }
        }

        return !$hasExplicitStatus;
    }

    private function isAttending(?IFSCStartListStatus $status): bool
    {
        return $status?->isAttending() ?? true;
    }

    private function normalizeLastName(string $lastName): string
    {
        return mb_convert_case($lastName, MB_CASE_TITLE, 'UTF-8');
    }

    private function readInt(mixed $value): ?int
    {
        return is_int($value) ? $value : null;
    }

    private function readString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
