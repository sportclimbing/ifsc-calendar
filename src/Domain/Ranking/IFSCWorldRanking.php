<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Ranking;

use Closure;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStarter;

final readonly class IFSCWorldRanking
{
    public function __construct(
        private IFSCWorldRankingProviderInterface $rankProvider,
    ) {
    }

    /**
     * @return IFSCStarter[]
     * @throws IFSCWorldRankingException
     */
    public function getAthletesByScore(): array
    {
        $scores = [];
        $athletes = [];

        foreach ($this->fetchWorldRankCategories() as $worldRankCategory) {
            foreach ($this->fetchWorldRankForCategory($worldRankCategory->id) as $athlete) {
                if (!isset($scores[$athlete->athlete_id])) {
                    $scores[$athlete->athlete_id] = 0;
                }

                $scores[$athlete->athlete_id] += $athlete->score;

                $athletes[$athlete->athlete_id] = new IFSCStarter(
                    firstName: $athlete->firstname,
                    lastName: $athlete->lastname,
                    country: $athlete->country,
                    photoUrl: $athlete->photo_url ?? null,
                );
            }
        }

        foreach ($scores as $athleteId => $score) {
            $athletes[$athleteId]->score = $score;
        }

        usort($athletes, $this->sortByScore());

        return $athletes;
    }

    /**
     * @return IFSCWorldRankCategory[]
     * @throws IFSCWorldRankingException
     */
    private function fetchWorldRankCategories(): array
    {
        return $this->rankProvider->fetchWorldRankCategories();
    }

    /**
     * @return array<mixed>
     * @throws IFSCWorldRankingException
     */
    private function fetchWorldRankForCategory(int $categoryId): array
    {
        return $this->rankProvider->fetchWorldRankForCategory($categoryId);
    }

    private function sortByScore(): Closure
    {
        return static fn (IFSCStarter $athlete1, IFSCStarter $athlete2): int =>  $athlete2->score <=> $athlete1->score;
    }
}
