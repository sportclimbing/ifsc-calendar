<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\StartList;

use Closure;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRanking;
use nicoSWD\IfscCalendar\Domain\Ranking\IFSCWorldRankingException;

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
     * @throws IFSCWorldRankingException
     */
    public function buildStartList(int $eventId): array
    {
        $startList = [];

        foreach ($this->getStartListForEvent($eventId) as $starter) {
            foreach ($this->worldRanking->getAthletesByScore() as $athlete) {
                if ($starter->equals($athlete)) {
                    $starter->score = $athlete->score;
                    $starter->photoUrl = $athlete->photoUrl;

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
