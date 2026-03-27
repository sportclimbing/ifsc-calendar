<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Infrastructure\Athlete;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteProviderInterface;
use SportClimbing\IfscCalendar\Infrastructure\Athlete\CachedAthleteProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CachedAthleteProviderTest extends TestCase
{
    #[Test] public function repeated_requests_for_same_athlete_use_cache(): void
    {
        $apiProvider = new class () implements IFSCAthleteProviderInterface {
            public int $requestCount = 0;

            #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
            {
                $this->requestCount++;

                return self::athlete($athleteId);
            }

            private static function athlete(int $athleteId): IFSCAthlete
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
                    photoUrl: null,
                    actionPhotoUrl: null,
                    disciplinePodiums: [],
                    worldChampionshipsDisciplinePodiums: [],
                    continentalChampionshipsDisciplinePodiums: [],
                    allResults: [],
                    cupRankings: [],
                );
            }
        };

        $provider = new CachedAthleteProvider(
            provider: $apiProvider,
            cache: new ArrayAdapter(),
            cacheTtlSeconds: 3600,
        );

        $athlete1 = $provider->fetchAthlete(13021);
        $athlete2 = $provider->fetchAthlete(13021);

        $this->assertSame(1, $apiProvider->requestCount);
        $this->assertSame($athlete1->id, $athlete2->id);
        $this->assertSame($athlete1->firstName, $athlete2->firstName);
    }

    #[Test] public function different_athletes_are_cached_independently(): void
    {
        $apiProvider = new class () implements IFSCAthleteProviderInterface {
            /** @var array<int,int> */
            public array $requestsByAthleteId = [];

            #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
            {
                $this->requestsByAthleteId[$athleteId] = ($this->requestsByAthleteId[$athleteId] ?? 0) + 1;

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
                    photoUrl: null,
                    actionPhotoUrl: null,
                    disciplinePodiums: [],
                    worldChampionshipsDisciplinePodiums: [],
                    continentalChampionshipsDisciplinePodiums: [],
                    allResults: [],
                    cupRankings: [],
                );
            }
        };

        $provider = new CachedAthleteProvider(
            provider: $apiProvider,
            cache: new ArrayAdapter(),
            cacheTtlSeconds: 3600,
        );

        $provider->fetchAthlete(1);
        $provider->fetchAthlete(2);
        $provider->fetchAthlete(1);
        $provider->fetchAthlete(2);

        $this->assertSame(1, $apiProvider->requestsByAthleteId[1]);
        $this->assertSame(1, $apiProvider->requestsByAthleteId[2]);
    }
}
