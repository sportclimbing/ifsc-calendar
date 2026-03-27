<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Ranking;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;

final readonly class IFSCAthleteRankingCalculator
{
    private const string WORLD_CUP_EVENT_NAME_PATTERN = '/\bworld\s?cup\b/i';

    // Bayesian prior to keep tiny samples from dominating.
    private const int PRIOR_WEIGHT = 5;
    private const float PRIOR_POINTS_MEAN = 0.04; // Equivalent to an average rank of ~25.
    private const float PARTICIPATION_BONUS_WEIGHT = 0.1;

    public function calculateScore(IFSCAthlete $athlete): float
    {
        $ranks = $this->extractWorldCupRanks($athlete);
        $participationCount = count($ranks);

        if ($participationCount === 0) {
            return 0;
        }

        $rankPoints = array_map(
            static fn (int $rank): float => 1 / $rank,
            $ranks,
        );
        $totalPoints = array_sum($rankPoints);

        $qualityScore = (
            $totalPoints + (self::PRIOR_WEIGHT * self::PRIOR_POINTS_MEAN)
        ) / ($participationCount + self::PRIOR_WEIGHT);

        // Diminishing-return bonus: consistency matters, but quality stays dominant.
        $participationBonus = 1 + (log(1 + $participationCount) * self::PARTICIPATION_BONUS_WEIGHT);

        return $qualityScore * $participationBonus * 100;
    }

    /**
     * @return int[]
     */
    private function extractWorldCupRanks(IFSCAthlete $athlete): array
    {
        $ranks = [];

        foreach ($athlete->allResults as $result) {
            if (!preg_match(self::WORLD_CUP_EVENT_NAME_PATTERN, $result->eventName)) {
                continue;
            }

            if ($result->rank <= 0) {
                continue;
            }

            $ranks[] = $result->rank;
        }

        return $ranks;
    }
}
