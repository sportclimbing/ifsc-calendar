<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Ranking;

interface IFSCWorldRankingProviderInterface
{
    /** @throws IFSCWorldRankingException */
    public function fetchWorldRankCategories(): array;

    /** @throws IFSCWorldRankingException */
    public function fetchWorldRankForCategory(int $categoryId): array;
}
