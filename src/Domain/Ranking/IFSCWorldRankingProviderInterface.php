<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Ranking;

interface IFSCWorldRankingProviderInterface
{
    /**
     * @return IFSCWorldRankCategory[]
     * @throws IFSCWorldRankingException
     */
    public function fetchWorldRankCategories(): array;

    /**
     * @return array<mixed>
     * @throws IFSCWorldRankingException
     */
    public function fetchWorldRankForCategory(int $categoryId): array;
}
