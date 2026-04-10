<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Domain\Ranking;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteResult;
use SportClimbing\IfscCalendar\Domain\Ranking\IFSCAthleteRankingCalculator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCAthleteRankingCalculatorTest extends TestCase
{
    #[Test] public function athletes_without_world_cup_results_have_zero_score(): void
    {
        $calculator = new IFSCAthleteRankingCalculator();
        $athlete = $this->athleteWithResults([
            new IFSCAthleteResult(
                season: '2025',
                rank: 1,
                discipline: 'boulder',
                eventName: 'IFSC World Championships Seoul 2025',
                eventId: 1417,
                dCat: 7,
                date: '2025-09-28',
                categoryName: 'Women',
                resultUrl: '/api/v1/events/1417/result/7',
            ),
        ]);

        $this->assertSame(0.0, $calculator->calculateScore($athlete));
    }

    #[Test] public function high_quality_results_outperform_many_low_results(): void
    {
        $calculator = new IFSCAthleteRankingCalculator();

        $athleteWithTopResults = $this->athleteWithResults([
            $this->worldCupResult(rank: 1, eventId: 1405),
            $this->worldCupResult(rank: 2, eventId: 1411),
        ]);

        $athleteWithManyLowResults = $this->athleteWithResults([
            $this->worldCupResult(rank: 20, eventId: 1501),
            $this->worldCupResult(rank: 20, eventId: 1502),
            $this->worldCupResult(rank: 20, eventId: 1503),
            $this->worldCupResult(rank: 20, eventId: 1504),
            $this->worldCupResult(rank: 20, eventId: 1505),
            $this->worldCupResult(rank: 20, eventId: 1506),
            $this->worldCupResult(rank: 20, eventId: 1507),
            $this->worldCupResult(rank: 20, eventId: 1508),
            $this->worldCupResult(rank: 20, eventId: 1509),
            $this->worldCupResult(rank: 20, eventId: 1510),
        ]);

        $this->assertGreaterThan(
            $calculator->calculateScore($athleteWithManyLowResults),
            $calculator->calculateScore($athleteWithTopResults),
        );
    }

    #[Test] public function consistent_good_results_are_rewarded(): void
    {
        $calculator = new IFSCAthleteRankingCalculator();

        $athleteWithSingleGoodResult = $this->athleteWithResults([
            $this->worldCupResult(rank: 2, eventId: 1601),
        ]);

        $athleteWithManyGoodResults = $this->athleteWithResults([
            $this->worldCupResult(rank: 4, eventId: 1602),
            $this->worldCupResult(rank: 4, eventId: 1603),
            $this->worldCupResult(rank: 4, eventId: 1604),
            $this->worldCupResult(rank: 4, eventId: 1605),
            $this->worldCupResult(rank: 4, eventId: 1606),
        ]);

        $this->assertGreaterThan(
            $calculator->calculateScore($athleteWithSingleGoodResult),
            $calculator->calculateScore($athleteWithManyGoodResults),
        );
    }

    /** @param IFSCAthleteResult[] $results */
    private function athleteWithResults(array $results): IFSCAthlete
    {
        return new IFSCAthlete(
            id: 13021,
            firstName: 'Annie',
            lastName: 'SANDERS',
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
            photoUrl: null,
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
            eventName: "IFSC World Cup Event {$eventId}",
            eventId: $eventId,
            dCat: 7,
            date: '2025-01-01',
            categoryName: 'Women',
            resultUrl: "/api/v1/events/{$eventId}/result/7",
        );
    }
}
