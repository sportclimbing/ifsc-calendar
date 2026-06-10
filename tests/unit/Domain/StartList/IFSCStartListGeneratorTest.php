<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Domain\StartList;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteProviderInterface;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteResult;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteService;
use SportClimbing\IfscCalendar\Domain\Ranking\IFSCAthleteRankingCalculator;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteGender;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStarter;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListGenerator;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListProviderInterface;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDiscipline;

final class IFSCStartListGeneratorTest extends TestCase
{
    #[Test] public function starters_are_ranked_by_calculated_athlete_score(): void
    {
        $startListProvider = new class () implements IFSCStartListProviderInterface {
            /** @return IFSCStarter[] */
            #[Override] public function fetchStartListForEvent(int $eventId): array
            {
                return [
                    new IFSCStarter(athleteId: 3, firstName: 'C', lastName: 'Last', country: 'USA', disciplines: []),
                    new IFSCStarter(athleteId: 1, firstName: 'A', lastName: 'Last', country: 'USA', disciplines: []),
                    new IFSCStarter(athleteId: 2, firstName: 'B', lastName: 'Last', country: 'USA', disciplines: []),
                ];
            }
        };

        $athleteProvider = new class () implements IFSCAthleteProviderInterface {
            /** @var int[] */
            public array $requestedAthleteIds = [];

            #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
            {
                $this->requestedAthleteIds[] = $athleteId;

                return match ($athleteId) {
                    1 => $this->athlete($athleteId, 'https://photos/1', [
                        $this->worldCupResult(1, 2001),
                        $this->worldCupResult(2, 2002),
                    ]),
                    2 => $this->athlete($athleteId, 'https://photos/2', [
                        $this->worldCupResult(5, 2101),
                        $this->worldCupResult(5, 2102),
                        $this->worldCupResult(5, 2103),
                        $this->worldCupResult(5, 2104),
                    ]),
                    3 => $this->athlete($athleteId, 'https://photos/3', []),
                };
            }

            /**
             * @param IFSCAthleteResult[] $results
             */
            private function athlete(int $athleteId, string $photoUrl, array $results, string $gender = 'female'): IFSCAthlete
            {
                return new IFSCAthlete(
                    id: $athleteId,
                    firstName: "Athlete {$athleteId}",
                    lastName: 'Test',
                    birthday: null,
                    gender: $gender,
                    personalStory: null,
                    federation: null,
                    country: 'USA',
                    flagUrl: 'https://flags/USA.png',
                    city: null,
                    age: null,
                    height: null,
                    instagram: null,
                    nickname: null,
                    spokenLanguages: null,
                    photoUrl: $photoUrl,
                    actionPhotoUrl: null,
                    disciplinePodiums: [],
                    worldChampionshipsDisciplinePodiums: [],
                    continentalChampionshipsDisciplinePodiums: [],
                    allResults: $results,
                    cupRankings: [],
                );
            }

            private function worldCupResult(int $rank, int $eventId): IFSCAthleteResult
            {
                return new IFSCAthleteResult(
                    season: '2025',
                    rank: $rank,
                    discipline: 'boulder',
                    eventName: "IFSC World Cup {$eventId}",
                    eventId: $eventId,
                    dCat: 7,
                    date: '2025-06-01',
                    categoryName: 'Women',
                    resultUrl: "/api/v1/events/{$eventId}/result/7",
                );
            }
        };

        $generator = new IFSCStartListGenerator(
            startListProvider: $startListProvider,
            athleteService: new IFSCAthleteService($athleteProvider),
            rankingCalculator: new IFSCAthleteRankingCalculator(),
        );

        $result = $generator->buildStartList(999);
        $startList = $result->starters;

        $this->assertSame([3, 1, 2], $athleteProvider->requestedAthleteIds);
        $this->assertSame([1, 2, 3], array_map(static fn (IFSCStarter $starter): int => $starter->athleteId, $startList));
        $this->assertSame(IFSCAthleteGender::WOMEN, $startList[0]->gender);
        $this->assertSame(IFSCAthleteGender::WOMEN, $startList[1]->gender);
        $this->assertSame(IFSCAthleteGender::WOMEN, $startList[2]->gender);

        $this->assertSame('https://photos/1', $startList[0]->photoUrl);
        $this->assertSame('https://photos/2', $startList[1]->photoUrl);
        $this->assertSame('https://photos/3', $startList[2]->photoUrl);
        $this->assertGreaterThan($startList[1]->score, $startList[0]->score);
        $this->assertGreaterThan($startList[2]->score, $startList[1]->score);
    }

    #[Test] public function top_20_of_each_gender_selected_when_both_have_enough(): void
    {
        $menIds = range(1, 25);
        $womenIds = range(26, 50);

        $startListProvider = $this->createProvider($menIds, $womenIds);
        $athleteProvider = $this->createAthleteProvider($menIds, $womenIds);

        $generator = new IFSCStartListGenerator(
            startListProvider: $startListProvider,
            athleteService: new IFSCAthleteService($athleteProvider),
            rankingCalculator: new IFSCAthleteRankingCalculator(),
        );

        $result = $generator->buildStartList(999);
        $startList = $result->starters;

        $this->assertSame(50, $result->total);
        $this->assertCount(40, $startList);

        $menInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::MEN);
        $womenInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::WOMEN);

        $this->assertCount(20, $menInList);
        $this->assertCount(20, $womenInList);

        // Top 20 men (lowest IDs = best ranks) should be in the list
        $menAthleteIds = array_map(static fn (IFSCStarter $s): int => $s->athleteId, array_values($menInList));
        foreach (range(1, 20) as $id) {
            $this->assertContains($id, $menAthleteIds);
        }
        // Men 21-25 should NOT be in the list
        foreach (range(21, 25) as $id) {
            $this->assertNotContains($id, $menAthleteIds);
        }

        // Top 20 women should be in the list
        $womenAthleteIds = array_map(static fn (IFSCStarter $s): int => $s->athleteId, array_values($womenInList));
        foreach (range(26, 45) as $id) {
            $this->assertContains($id, $womenAthleteIds);
        }
        // Women 46-50 should NOT be in the list
        foreach (range(46, 50) as $id) {
            $this->assertNotContains($id, $womenAthleteIds);
        }
    }

    #[Test] public function men_shortfall_filled_by_extra_women(): void
    {
        $menIds = range(1, 10);
        $womenIds = range(11, 40);

        $startListProvider = $this->createProvider($menIds, $womenIds);
        $athleteProvider = $this->createAthleteProvider($menIds, $womenIds);

        $generator = new IFSCStartListGenerator(
            startListProvider: $startListProvider,
            athleteService: new IFSCAthleteService($athleteProvider),
            rankingCalculator: new IFSCAthleteRankingCalculator(),
        );

        $result = $generator->buildStartList(999);
        $startList = $result->starters;

        $this->assertSame(40, $result->total);
        $this->assertCount(40, $startList);

        $menInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::MEN);
        $womenInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::WOMEN);

        // Only 10 men exist, so all 10 should be included
        $this->assertCount(10, $menInList);
        // 30 women should fill the rest
        $this->assertCount(30, $womenInList);

        // All 10 men should be present
        $menAthleteIds = array_map(static fn (IFSCStarter $s): int => $s->athleteId, array_values($menInList));
        foreach (range(1, 10) as $id) {
            $this->assertContains($id, $menAthleteIds);
        }
        // All 30 women should be present (no women left out)
        $womenAthleteIds = array_map(static fn (IFSCStarter $s): int => $s->athleteId, array_values($womenInList));
        foreach (range(11, 40) as $id) {
            $this->assertContains($id, $womenAthleteIds);
        }
    }

    #[Test] public function women_shortfall_filled_by_extra_men(): void
    {
        $menIds = range(1, 30);
        $womenIds = range(31, 40);

        $startListProvider = $this->createProvider($menIds, $womenIds);
        $athleteProvider = $this->createAthleteProvider($menIds, $womenIds);

        $generator = new IFSCStartListGenerator(
            startListProvider: $startListProvider,
            athleteService: new IFSCAthleteService($athleteProvider),
            rankingCalculator: new IFSCAthleteRankingCalculator(),
        );

        $result = $generator->buildStartList(999);
        $startList = $result->starters;

        $this->assertSame(40, $result->total);
        $this->assertCount(40, $startList);

        $menInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::MEN);
        $womenInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::WOMEN);

        // 30 men (top 20 + 10 filling women shortfall)
        $this->assertCount(30, $menInList);
        // Only 10 women exist, all included
        $this->assertCount(10, $womenInList);

        $womenAthleteIds = array_map(static fn (IFSCStarter $s): int => $s->athleteId, array_values($womenInList));
        foreach (range(31, 40) as $id) {
            $this->assertContains($id, $womenAthleteIds);
        }
    }

    #[Test] public function both_genders_below_20_returns_all_available(): void
    {
        $menIds = range(1, 8);
        $womenIds = range(9, 15);

        $startListProvider = $this->createProvider($menIds, $womenIds);
        $athleteProvider = $this->createAthleteProvider($menIds, $womenIds);

        $generator = new IFSCStartListGenerator(
            startListProvider: $startListProvider,
            athleteService: new IFSCAthleteService($athleteProvider),
            rankingCalculator: new IFSCAthleteRankingCalculator(),
        );

        $result = $generator->buildStartList(999);
        $startList = $result->starters;

        $this->assertSame(15, $result->total);
        $this->assertCount(15, $startList);

        $menInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::MEN);
        $womenInList = array_filter($startList, fn (IFSCStarter $s): bool => $s->gender === IFSCAthleteGender::WOMEN);

        $this->assertCount(8, $menInList);
        $this->assertCount(7, $womenInList);
    }

    #[Test] public function null_category_athletes_are_excluded(): void
    {
        $startListProvider = new class () implements IFSCStartListProviderInterface {
            /** @return IFSCStarter[] */
            #[Override] public function fetchStartListForEvent(int $eventId): array
            {
                return [
                    new IFSCStarter(athleteId: 1, firstName: 'Male', lastName: 'A', country: 'USA', disciplines: []),
                    new IFSCStarter(athleteId: 2, firstName: 'Null', lastName: 'B', country: 'USA', disciplines: []),
                    new IFSCStarter(athleteId: 3, firstName: 'Female', lastName: 'C', country: 'USA', disciplines: []),
                ];
            }
        };

        $athleteProvider = new class () implements IFSCAthleteProviderInterface {
            #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
            {
                return new IFSCAthlete(
                    id: $athleteId,
                    firstName: "Athlete {$athleteId}",
                    lastName: 'Test',
                    birthday: null,
                    gender: match ($athleteId) {
                        1 => 'male',
                        2 => 'other',
                        3 => 'female',
                    },
                    personalStory: null,
                    federation: null,
                    country: 'USA',
                    flagUrl: 'https://flags/USA.png',
                    city: null,
                    age: null,
                    height: null,
                    instagram: null,
                    nickname: null,
                    spokenLanguages: null,
                    photoUrl: null,
                    actionPhotoUrl: null,
                    disciplinePodiums: [],
                    worldChampionshipsDisciplinePodiums: [],
                    continentalChampionshipsDisciplinePodiums: [],
                    allResults: [
                        new IFSCAthleteResult(
                            season: '2025',
                            rank: $athleteId, // Different ranks for different scores
                            discipline: 'boulder',
                            eventName: "IFSC World Cup {$athleteId}",
                            eventId: $athleteId,
                            dCat: 7,
                            date: '2025-06-01',
                            categoryName: 'Women',
                            resultUrl: "/api/v1/events/{$athleteId}/result/7",
                        ),
                    ],
                    cupRankings: [],
                );
            }
        };

        $generator = new IFSCStartListGenerator(
            startListProvider: $startListProvider,
            athleteService: new IFSCAthleteService($athleteProvider),
            rankingCalculator: new IFSCAthleteRankingCalculator(),
        );

        $result = $generator->buildStartList(999);
        $startList = $result->starters;

        $this->assertSame(3, $result->total);

        $includedIds = array_map(static fn (IFSCStarter $s): int => $s->athleteId, $startList);
        $this->assertContains(1, $includedIds); // male → MEN
        $this->assertContains(3, $includedIds); // female → WOMEN
        $this->assertNotContains(2, $includedIds); // other → null, excluded
    }

    /** @param int[] $menIds @param int[] $womenIds */
    private function createProvider(array $menIds, array $womenIds): IFSCStartListProviderInterface
    {
        $starters = [];

        foreach ($menIds as $id) {
            $starters[] = new IFSCStarter(athleteId: $id, firstName: "M{$id}", lastName: 'Test', country: 'USA', disciplines: []);
        }
        foreach ($womenIds as $id) {
            $starters[] = new IFSCStarter(athleteId: $id, firstName: "W{$id}", lastName: 'Test', country: 'USA', disciplines: []);
        }

        return new class ($starters) implements IFSCStartListProviderInterface {
            /** @param IFSCStarter[] $starters */
            public function __construct(private array $starters) {}
            /** @return IFSCStarter[] */
            #[Override] public function fetchStartListForEvent(int $eventId): array { return $this->starters; }
        };
    }

    /** @param int[] $menIds @param int[] $womenIds */
    private function createAthleteProvider(array $menIds, array $womenIds): IFSCAthleteProviderInterface
    {
        $isMale = array_flip($menIds);

        return new class ($isMale, $menIds, $womenIds) implements IFSCAthleteProviderInterface {
            /**
             * @param array<int, int> $isMale
             * @param int[] $menIds
             * @param int[] $womenIds
             */
            public function __construct(
                private array $isMale,
                private array $menIds,
                private array $womenIds,
            ) {}

            #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
            {
                $athleteIsMale = isset($this->isMale[$athleteId]);
                // Rank within gender: 1 = best (first ID in the list)
                $genderIds = $athleteIsMale ? $this->menIds : $this->womenIds;
                $rank = (int) array_search($athleteId, $genderIds, strict: true) + 1;

                return new IFSCAthlete(
                    id: $athleteId,
                    firstName: "Athlete {$athleteId}",
                    lastName: 'Test',
                    birthday: null,
                    gender: $athleteIsMale ? 'male' : 'female',
                    personalStory: null,
                    federation: null,
                    country: 'USA',
                    flagUrl: 'https://flags/USA.png',
                    city: null,
                    age: null,
                    height: null,
                    instagram: null,
                    nickname: null,
                    spokenLanguages: null,
                    photoUrl: "https://photos/{$athleteId}",
                    actionPhotoUrl: null,
                    disciplinePodiums: [],
                    worldChampionshipsDisciplinePodiums: [],
                    continentalChampionshipsDisciplinePodiums: [],
                    allResults: [
                        new IFSCAthleteResult(
                            season: '2025',
                            rank: $rank,
                            discipline: IFSCDiscipline::BOULDER->value,
                            eventName: "IFSC World Cup {$athleteId}",
                            eventId: $athleteId,
                            dCat: 7,
                            date: '2025-06-01',
                            categoryName: $athleteIsMale ? 'Men' : 'Women',
                            resultUrl: "/api/v1/events/{$athleteId}/result/7",
                        ),
                    ],
                    cupRankings: [],
                );
            }
        };
    }
}
