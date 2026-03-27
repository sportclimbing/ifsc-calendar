<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Domain\Athlete;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteFederation;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteProviderInterface;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteService;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCAthleteServiceTest extends TestCase
{
    #[Test] public function athlete_is_fetched_via_provider(): void
    {
        $expectedAthlete = new IFSCAthlete(
            id: 13021,
            firstName: 'Annie',
            lastName: 'SANDERS',
            birthday: '2007-07-22',
            gender: 'female',
            personalStory: 'story',
            federation: new IFSCAthleteFederation(20, 'USA Climbing', 'USAC'),
            country: 'USA',
            flagUrl: 'https://flags/USA.png',
            city: 'Fort Worth',
            age: 18,
            height: 161,
            instagram: 'https://www.instagram.com/annie.climbs.sanders/',
            nickname: 'Annie',
            spokenLanguages: 'English',
            photoUrl: 'https://photos/profile',
            actionPhotoUrl: 'https://photos/action',
            disciplinePodiums: [],
            worldChampionshipsDisciplinePodiums: [],
            continentalChampionshipsDisciplinePodiums: [],
            allResults: [],
            cupRankings: [],
        );

        $provider = new class ($expectedAthlete) implements IFSCAthleteProviderInterface {
            public int $requestedAthleteId = 0;

            public function __construct(
                private IFSCAthlete $athlete,
            ) {
            }

            #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
            {
                $this->requestedAthleteId = $athleteId;

                return $this->athlete;
            }
        };

        $service = new IFSCAthleteService($provider);
        $athlete = $service->fetchAthlete(13021);

        $this->assertSame(13021, $provider->requestedAthleteId);
        $this->assertSame($expectedAthlete, $athlete);
    }
}
