<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Ranking;

use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRankCategory;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRankingException;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRankingProviderInterface;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;
use Override;

final readonly class IFSCApiWorldRankingProvider implements IFSCWorldRankingProviderInterface
{
    private const string IFSC_WORLD_RANK_CATEGORIES_ENDPOINT = 'https://ifsc.results.info/api/v1/cuwr';

    private const string IFSC_WORLD_RANK_CATEGORY_INFO_ENDPOINT = 'https://ifsc.results.info/api/v1/cuwr/%d';

    public function __construct(
        private IFSCApiClient $apiClient,
    ) {
    }

    /** @inheritdoc */
    #[Override]
    public function fetchWorldRankCategories(): array
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                self::IFSC_WORLD_RANK_CATEGORIES_ENDPOINT
            );
        } catch (HttpException|IFSCApiClientException $e) {
            throw new IFSCWorldRankingException(
                "Unable to fetch world rank categories: {$e->getMessage()}"
            );
        }

        $categories = [];

        foreach ($response as $category) {
            $categories[] = new IFSCWorldRankCategory(
                id: $category->dcat_id,
                name: $category->name,
            );
        }

        return $categories;
    }

    /** @inheritdoc */
    #[Override]
    public function fetchWorldRankForCategory(int $categoryId): array
    {
        try {
            $response = $this->apiClient->authenticatedGet(
                sprintf(self::IFSC_WORLD_RANK_CATEGORY_INFO_ENDPOINT, $categoryId),
            );
        } catch (HttpException|IFSCApiClientException $e) {
            throw new IFSCWorldRankingException(
                "Unable to fetch world rank category: {$e->getMessage()}"
            );
        }

        return $response->ranking;
    }
}
