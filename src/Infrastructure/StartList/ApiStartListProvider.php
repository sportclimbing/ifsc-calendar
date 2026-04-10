<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\StartList;

use Closure;
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

        return array_values(array_map($this->convertToStarterObject(), $includedAthletes));
    }

    private function convertToStarterObject(): Closure
    {
        return fn (object $athlete): IFSCStarter => new IFSCStarter(
            athleteId: $athlete->athlete_id,
            firstName: $athlete->firstname,
            lastName: $this->normalizeLastName($athlete->lastname),
            country: $athlete->country,
        );
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
}
