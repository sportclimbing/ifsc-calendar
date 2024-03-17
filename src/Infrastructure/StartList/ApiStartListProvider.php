<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\StartList;

use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStarter;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStartListException;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStartListProviderInterface;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;

final readonly class ApiStartListProvider implements IFSCStartListProviderInterface
{
    private const string IFSC_STARTERS_API_ENDPOINT = 'https://components.ifsc-climbing.org/results-api.php?api=starters&event_id=%d';

    public function __construct(
        private IFSCApiClient $apiClient,
    ) {
    }

    /** @inheritdoc */
    #[Override]
    public function fetchStartListForEvent(int $eventId): array
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

        $convertToStarter = static fn (object $athlete): IFSCStarter => new IFSCStarter(
            firstName: $athlete->firstname,
            lastName: $athlete->lastname,
            country: $athlete->country,
        );

        return array_map($convertToStarter, $athletes);
    }
}
