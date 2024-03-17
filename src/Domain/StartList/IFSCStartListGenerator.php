<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\StartList;

use Closure;

final readonly class IFSCStartListGenerator
{
    public function __construct(
        private IFSCStartListProviderInterface $startListProvider,
    ) {
    }

    /**
     * @return IFSCStarter[]
     * @throws IFSCStartListException
     */
    public function buildStartList(int $eventId): array
    {
        $athletes = [];
        $scores = [];
        $startList = [];

        foreach ($this->getWorldRankCategories() as $worldRankCategory) {
            foreach ($this->fetchRankForCategory($worldRankCategory) as $athlete) {
                if (!isset($scores[$athlete->athlete_id])) {
                    $scores[$athlete->athlete_id] = 0;
                }

                $scores[$athlete->athlete_id] += $athlete->score;

                $athletes[$athlete->athlete_id] = [
                    'id' => $athlete->athlete_id,
                    'firstname' => $athlete->firstname,
                    'lastname' => $athlete->lastname,
                    'country' => $athlete->country,
                    'photo_url' => $athlete->photo_url ?? null,
                ];
            }
        }

        foreach ($scores as $athleteId => $score) {
            $athletes[$athleteId]['score'] = $score;
        }

        usort($athletes, static fn (array $athlete1, array $athlete2): int => $athlete2['score'] <=> $athlete1['score']);

        foreach ($this->getStartListForEvent($eventId) as $starter) {
            foreach ($athletes as $athlete) {
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
    private function fetchRankForCategory(object $worldRankCategory): array
    {
        return $this->startListProvider->fetchWorldRankForCategory($worldRankCategory->dcat_id);
    }

    /** @throws IFSCStartListException */
    private function getWorldRankCategories(): array
    {
        return $this->startListProvider->fetchWorldRankCategories();
    }

    /** @throws IFSCStartListException */
    private function getStartListForEvent(int $eventId): array
    {
        return $this->startListProvider->fetchStartListForEvent($eventId);
    }
}
