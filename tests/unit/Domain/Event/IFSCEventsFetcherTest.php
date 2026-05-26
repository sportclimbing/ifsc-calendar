<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Domain\Event;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteProviderInterface;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteService;
use SportClimbing\IfscCalendar\Domain\Calendar\SiteURLBuilder;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use SportClimbing\IfscCalendar\Domain\DomainEvent\Event;
use SportClimbing\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEventFactory;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEventSlug;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEventsFetcher;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Ranking\IFSCAthleteRankingCalculator;
use SportClimbing\IfscCalendar\Domain\Round\IFSCAverageRoundDuration;
use SportClimbing\IfscCalendar\Domain\Round\IFSCAverageRoundDurationLookupKey;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundFactory;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use SportClimbing\IfscCalendar\Domain\Round\IFSCSameStreamRoundsMerger;
use SportClimbing\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListGenerator;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListProviderInterface;
use SportClimbing\IfscCalendar\Domain\Stream\LiveStream;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;
use SportClimbing\IfscCalendar\Domain\YouTube\YouTubeLiveStreamFinderInterface;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class IFSCEventsFetcherTest extends TestCase
{
    #[Test] public function speed_relay_is_parsed_as_a_dedicated_discipline(): void
    {
        $schedulePath = $this->createSchedulePath([
            [
                'event_id' => 9001,
                'event_name' => 'IFSC Relay Test Event 2026',
                'league_name' => 'World Cups and World Championships',
                'local_start_date' => '2026-05-26',
                'local_end_date' => '2026-05-27',
                'timezone' => 'Europe/Madrid',
                'location' => 'Madrid',
                'country' => 'ES',
                'disciplines' => ['speed_relay'],
                'categories' => ['men'],
            ],
        ]);

        try {
            $events = $this->createFetcher()->fetchEventsForSeason(
                season: IFSCSeasonYear::SEASON_2026,
                selectedLeagues: ['World Cups and World Championships'],
                schedulePath: $schedulePath,
            );
        } finally {
            @unlink($schedulePath);
        }

        $this->assertCount(1, $events);
        $this->assertSame([IFSCDiscipline::SPEED_RELAY], $events[0]->disciplines);
    }

    #[Test] public function speed_relay_round_payloads_produce_speed_relay_round_names(): void
    {
        $schedulePath = $this->createSchedulePath([
            [
                'event_id' => 9002,
                'event_name' => 'IFSC Relay Round Payload Test 2026',
                'league_name' => 'World Cups and World Championships',
                'local_start_date' => '2026-05-26',
                'local_end_date' => '2026-05-27',
                'timezone' => 'Europe/Madrid',
                'location' => 'Madrid',
                'country' => 'ES',
                'disciplines' => ['speed_relay'],
                'categories' => [[
                    'rounds' => [[
                        'discipline' => 'speed_relay',
                        'kind' => 'qualification',
                        'category' => 'men',
                    ]],
                ]],
            ],
        ]);

        try {
            $events = $this->createFetcher()->fetchEventsForSeason(
                season: IFSCSeasonYear::SEASON_2026,
                selectedLeagues: ['World Cups and World Championships'],
                schedulePath: $schedulePath,
            );
        } finally {
            @unlink($schedulePath);
        }

        $this->assertCount(1, $events);
        $this->assertCount(1, $events[0]->rounds);
        $this->assertSame("Men's Speed Relay Qualification", $events[0]->rounds[0]->name);
    }

    private function createFetcher(): IFSCEventsFetcher
    {
        $tagsParser = new IFSCTagsParser();
        $roundNameNormalizer = new IFSCRoundNameNormalizer();

        $roundFactory = new IFSCRoundFactory(
            tagsParser: $tagsParser,
            liveStreamFinder: new class () implements YouTubeLiveStreamFinderInterface {
                #[Override] public function findLiveStream(
                    IFSCEventInfo $event,
                    string $roundName,
                ): LiveStream {
                    return new LiveStream();
                }
            },
            averageRoundDuration: new IFSCAverageRoundDuration(
                new IFSCAverageRoundDurationLookupKey(),
            ),
        );

        $eventFactory = new IFSCEventFactory(
            siteURLBuilder: new SiteURLBuilder('https://ifsc.stream/{season}/{event_id}/{slug}'),
            startListGenerator: new IFSCStartListGenerator(
                startListProvider: new class () implements IFSCStartListProviderInterface {
                    #[Override] public function fetchStartListForEvent(int $eventId): array
                    {
                        return [];
                    }
                },
                athleteService: new IFSCAthleteService(
                    athleteProvider: new class () implements IFSCAthleteProviderInterface {
                        #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
                        {
                            throw new RuntimeException('Athletes are not expected in this test');
                        }
                    },
                ),
                rankingCalculator: new IFSCAthleteRankingCalculator(),
            ),
            sameStreamRoundsMerger: new IFSCSameStreamRoundsMerger(
                tagsParser: $tagsParser,
                nameNormalizer: $roundNameNormalizer,
            ),
        );

        return new IFSCEventsFetcher(
            eventFactory: $eventFactory,
            roundFactory: $roundFactory,
            scheduleFactory: new IFSCScheduleFactory($tagsParser, $roundNameNormalizer),
            eventDispatcher: new class () implements EventDispatcherInterface {
                #[Override] public function dispatch(Event $event): void
                {
                }
            },
            eventSlug: new IFSCEventSlug(),
        );
    }

    /** @param array<int,array<string,mixed>> $events */
    private function createSchedulePath(array $events): string
    {
        $schedulePath = tempnam(sys_get_temp_dir(), 'ifsc-events-test-');
        if ($schedulePath === false) {
            throw new RuntimeException('Unable to create temporary schedule file');
        }

        $json = json_encode(['events' => $events], JSON_THROW_ON_ERROR);
        if (file_put_contents($schedulePath, $json) === false) {
            throw new RuntimeException('Unable to write temporary schedule file');
        }

        return $schedulePath;
    }
}
