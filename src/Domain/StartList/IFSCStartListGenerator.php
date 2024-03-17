<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\StartList;

use Closure;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRanking;

final readonly class IFSCStartListGenerator
{
    public function __construct(
        private IFSCStartListProviderInterface $startListProvider,
        private IFSCWorldRanking $worldRanking,
    ) {
    }

    /**
     * @return IFSCStarter[]
     * @throws IFSCStartListException
     */
    public function buildStartList(int $eventId): array
    {
        $startList = [];

        foreach ($this->getStartListForEvent($eventId) as $starter) {
            foreach ($this->worldRanking->getAthletesByScore() as $athlete) {
                if ($this->starterMatchesAthlete($starter, $athlete)) {
                    $starter->score = $athlete['score'];
                    $starter->photoUrl = $athlete['photo_url'];

                    $startList[] = $starter;

                    if (count($startList) === 20) {
                        break 2;
                    }
                }
            }
        }

        usort($startList, $this->sortByScore());

        return $startList;
    }

    private function starterMatchesAthlete(IFSCStarter $starter, array $athlete): bool
    {
        return
            $starter->firstName === $athlete['firstname'] &&
            $starter->lastName === $athlete['lastname'] &&
            $starter->country === $athlete['country'];
    }

    private function sortByScore(): Closure
    {
        return static fn (IFSCStarter $athlete1, IFSCStarter $athlete2): int => $athlete2->score <=> $athlete1->score;
    }

    /** @throws IFSCStartListException */
    private function getStartListForEvent(int $eventId): array
    {
        return $this->startListProvider->fetchStartListForEvent($eventId);
    }
}
