<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\StartList;

use Closure;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteException;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteService;
use SportClimbing\IfscCalendar\Domain\Ranking\IFSCAthleteRankingCalculator;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteCategory;

final readonly class IFSCStartListGenerator
{
    private const int PER_GENDER_MAX = 20;

    public function __construct(
        private IFSCStartListProviderInterface $startListProvider,
        private IFSCAthleteService $athleteService,
        private IFSCAthleteRankingCalculator $rankingCalculator,
    ) {
    }

    /**
     * @throws IFSCStartListException
     * @throws IFSCAthleteException
     */
    public function buildStartList(int $eventId): IFSCStartListResult
    {
        $startList = [];

        foreach ($this->getStartListForEvent($eventId) as $starter) {
            $athlete = $this->athleteService->fetchAthlete($starter->athleteId);
            $starter->score = $this->rankingCalculator->calculateScore($athlete);
            $starter->photoUrl = $athlete->photoUrl;
            $starter->instagram = $this->normalizeInstagram($athlete->instagram);
            $starter->category = $this->getCategory($athlete);

            $startList[] = $starter;
        }

        usort($startList, $this->sortByScore());

        return new IFSCStartListResult(
            starters: $this->selectTopByGender($startList),
            total: count($startList),
        );
    }

    /**
     * @param IFSCStarter[] $startList
     * @return IFSCStarter[]
     */
    private function selectTopByGender(array $startList): array
    {
        $men = $this->filterByGender($startList, IFSCAthleteCategory::MEN);
        $women = $this->filterByGender($startList, IFSCAthleteCategory::WOMEN);

        $selectedMen = $this->selectTopFromPool($men, array_slice($women, self::PER_GENDER_MAX));
        $selectedWomen = $this->selectTopFromPool($women, array_slice($men, self::PER_GENDER_MAX));

        $result = array_merge($selectedMen, $selectedWomen);
        usort($result, $this->sortByScore());

        return $result;
    }

    /**
     * @param IFSCStarter[] $pool
     * @param IFSCStarter[] $fillPool
     * @return IFSCStarter[]
     */
    private function selectTopFromPool(array $pool, array $fillPool): array
    {
        $selected = array_slice($pool, 0, self::PER_GENDER_MAX);
        $shortfall = self::PER_GENDER_MAX - count($selected);

        if ($shortfall > 0) {
            $selected = array_merge($selected, array_slice($fillPool, 0, $shortfall));
        }

        return $selected;
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

    /** @return IFSCStarter[] */
    public function filterByGender(array $startList, IFSCAthleteCategory $category): array
    {
        return array_values(array_filter($startList, fn (IFSCStarter $starter): bool => $starter->category === $category));
    }

    private function normalizeInstagram(?string $instagram): ?string
    {
        if ($instagram === null || $instagram === '') {
            return null;
        }

        if (str_contains($instagram, 'instagram.com/')) {
            preg_match('~instagram\.com/([^/?#]+)~', $instagram, $matches);
            return $matches[1] ?? null;
        }

        return ltrim($instagram, '@');
    }

    /**
     * @param IFSCAthlete $athlete
     * @return IFSCAthleteCategory|null
     */
    private function getCategory(IFSCAthlete $athlete): ?IFSCAthleteCategory
    {
        return match ($athlete->gender) {
            'male' => IFSCAthleteCategory::MEN,
            'female' => IFSCAthleteCategory::WOMEN,
            default => null,
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
