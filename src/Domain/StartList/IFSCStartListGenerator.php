<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\StartList;

use Closure;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteException;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteService;
use SportClimbing\IfscCalendar\Domain\Ranking\IFSCAthleteRankingCalculator;

final readonly class IFSCStartListGenerator
{
    private const int LIST_MAX_SIZE = 20;

    public function __construct(
        private IFSCStartListProviderInterface $startListProvider,
        private IFSCAthleteService $athleteService,
        private IFSCAthleteRankingCalculator $rankingCalculator,
    ) {
    }

    /**
     * @return IFSCStarter[]
     * @throws IFSCStartListException
     * @throws IFSCAthleteException
     */
    public function buildStartList(int $eventId): array
    {
        $startList = [];

        foreach ($this->getStartListForEvent($eventId) as $starter) {
            $athlete = $this->athleteService->fetchAthlete($starter->athleteId);
            $starter->score = $this->rankingCalculator->calculateScore($athlete);
            $starter->photoUrl = $athlete->photoUrl;

            $startList[] = $starter;
        }

        usort($startList, $this->sortByScore());

        return array_slice($startList, 0, self::LIST_MAX_SIZE);
    }

    private function sortByScore(): Closure
    {
        return static function (IFSCStarter $athlete1, IFSCStarter $athlete2): int {
            $scoreComparison = $athlete2->score <=> $athlete1->score;

            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            return $athlete1->athleteId <=> $athlete2->athleteId;
        };
    }

    /**
     * @return IFSCStarter[]
     * @throws IFSCStartListException
     */
    private function getStartListForEvent(int $eventId): array
    {
        return $this->startListProvider->fetchStartListForEvent($eventId);
    }
}
