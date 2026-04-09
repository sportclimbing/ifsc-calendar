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
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStarter;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListGenerator;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListProviderInterface;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCStartListGeneratorTest extends TestCase
{
    #[Test] public function starters_are_ranked_by_calculated_athlete_score(): void
    {
        $startListProvider = new class () implements IFSCStartListProviderInterface {
            /** @return IFSCStarter[] */
            #[Override] public function fetchStartListForEvent(int $eventId): array
            {
                return [
                    new IFSCStarter(athleteId: 3, firstName: 'C', lastName: 'Last', country: 'USA'),
                    new IFSCStarter(athleteId: 1, firstName: 'A', lastName: 'Last', country: 'USA'),
                    new IFSCStarter(athleteId: 2, firstName: 'B', lastName: 'Last', country: 'USA'),
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
            private function athlete(int $athleteId, string $photoUrl, array $results): IFSCAthlete
            {
                return new IFSCAthlete(
                    id: $athleteId,
                    firstName: "Athlete {$athleteId}",
                    lastName: 'Test',
                    birthday: null,
                    gender: 'female',
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
                    eventLocation: 'City',
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
        $this->assertSame('https://photos/1', $startList[0]->photoUrl);
        $this->assertSame('https://photos/2', $startList[1]->photoUrl);
        $this->assertSame('https://photos/3', $startList[2]->photoUrl);
        $this->assertGreaterThan($startList[1]->score, $startList[0]->score);
        $this->assertGreaterThan($startList[2]->score, $startList[1]->score);
    }
}
