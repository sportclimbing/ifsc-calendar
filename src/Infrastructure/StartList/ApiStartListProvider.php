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

    private const string IFSC_WORLD_RANK_CATEGORIES_ENDPOINT = 'https://ifsc.results.info/api/v1/cuwr';

    private const string IFSC_WORLD_RANK_CATEGORY_INFO_ENDPOINT = 'https://ifsc.results.info/api/v1/cuwr/%d';

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

        $startList = [];

        foreach ($athletes as $athlete) {
            $startList[] = new IFSCStarter(
                firstName: $athlete->firstname,
                lastName: $athlete->lastname,
                country: $athlete->country,
            );
        }

        return $startList;
    }

    /** @throws IFSCStartListException */
    public function fetchWorldRankCategories(): array
    {
        try {
            return $this->apiClient->authenticatedGet(
                self::IFSC_WORLD_RANK_CATEGORIES_ENDPOINT
            );
        } catch (HttpException|IFSCApiClientException $e) {
            throw new IFSCStartListException(
                "Unable to fetch world rank categories: {$e->getMessage()}"
            );
        }
    }

    /** @throws IFSCStartListException */
    public function fetchWorldRankForCategory(int $categoryId): array
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_WORLD_RANK_CATEGORY_INFO_ENDPOINT, $categoryId),
            );
        } catch (HttpException|IFSCApiClientException $e) {
            throw new IFSCStartListException(
                "Unable to fetch world rank category: {$e->getMessage()}"
            );
        }

        return $response->ranking;
    }
}
