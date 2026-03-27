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

        return array_map($this->convertToStarterObject(), $athletes);
    }

    private function convertToStarterObject(): Closure
    {
        return static fn (object $athlete): IFSCStarter => new IFSCStarter(
            athleteId: $athlete->athlete_id,
            firstName: $athlete->firstname,
            lastName: $athlete->lastname,
            country: $athlete->country,
        );
    }
}
