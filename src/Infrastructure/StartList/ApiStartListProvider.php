<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\StartList;

use nicoSWD\IfscCalendar\Domain\StartList\IFSCStarter;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStartListProviderInterface;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
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

    #[Override]
    /** @inheritdoc */
    public function fetchStartListForEvent(int $eventId): array
    {
        $athletes = $this->apiClient->fetchAuth(
            sprintf(self::IFSC_STARTERS_API_ENDPOINT, $eventId),
        );

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

    public function fetchWorldRankCategories(): array
    {
        return $this->apiClient->fetchAuth(
            self::IFSC_WORLD_RANK_CATEGORIES_ENDPOINT
        );
    }

    public function fetchWorldRankForCategory(int $categoryId): array
    {
        $response = $this->apiClient->fetchAuth(
            sprintf(self::IFSC_WORLD_RANK_CATEGORY_INFO_ENDPOINT, $categoryId),
        );

        return $response->ranking;
    }
}
