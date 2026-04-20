<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Domain\YouTube;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEventTagsRegex as Tag;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;
use SportClimbing\IfscCalendar\Domain\YouTube\YouTubeMatchScorer;
use SportClimbing\IfscCalendar\Domain\YouTube\YouTubeTextNormalizer;
use SportClimbing\IfscCalendar\Domain\YouTube\YouTubeVideo;

final class YouTubeMatchScorerTest extends TestCase
{
    private readonly IFSCTagsParser $tagsParser;
    private readonly YouTubeMatchScorer $matchScorer;

    #[Test] public function programmed_video_with_zero_duration_is_scored(): void
    {
        $video = $this->createVideo(
            title: "Women's Speed qualification || Salt Lake City 2026",
            duration: 0,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: '2026-05-21T12:00:00Z',
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags("Women's Speed Qualification"),
            event: $this->createEvent(
                eventName: 'IFSC World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
                localStartDate: '2026-05-20T08:00:00Z',
                localEndDate: '2026-05-22T20:00:00Z',
            ),
        );

        $this->assertNotNull($score);
        $this->assertGreaterThanOrEqual(14, $score);
    }

    #[Test] public function opposite_gender_is_rejected(): void
    {
        $video = $this->createVideo(
            title: "Men's Speed qualification || Salt Lake City 2026",
            duration: 90,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: '2026-05-21T12:00:00Z',
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags("Women's Speed Qualification"),
            event: $this->createEvent(
                eventName: 'IFSC World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
            ),
        );

        $this->assertNull($score);
    }

    #[Test] public function highlights_video_is_rejected(): void
    {
        $video = $this->createVideo(
            title: "Women's Speed qualification highlights || Salt Lake City 2026",
            duration: 4,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: null,
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags("Women's Speed Qualification"),
            event: $this->createEvent(
                eventName: 'IFSC World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
            ),
        );

        $this->assertNull($score);
    }

    #[Test] public function slc_alias_is_accepted_for_salt_lake_city_event(): void
    {
        $video = $this->createVideo(
            title: "Women's Speed qualification || SLC 2026",
            duration: 0,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: '2026-05-21T12:00:00Z',
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags("Women's Speed Qualification"),
            event: $this->createEvent(
                eventName: 'IFSC World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
            ),
        );

        $this->assertNotNull($score);
    }

    #[Test] public function paraclimbing_video_is_rejected_for_non_para_event(): void
    {
        $video = $this->createVideo(
            title: "Paraclimbing Speed qualification || Salt Lake City 2026",
            duration: 90,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: '2026-05-21T12:00:00Z',
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags("Women's Speed Qualification"),
            event: $this->createEvent(
                eventName: 'IFSC World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
            ),
        );

        $this->assertNull($score);
    }

    #[Test] public function paraclimbing_qualification_is_scored_for_para_event(): void
    {
        $video = $this->createVideo(
            title: 'Para Climbing qualification | Salt Lake City 2026',
            duration: 0,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: '2026-05-21T12:00:00Z',
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags('Paraclimbing Qualification'),
            event: $this->createEvent(
                eventName: 'IFSC Para Climbing World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
            ),
        );

        $this->assertNotNull($score);
    }

    #[Test] public function non_paraclimbing_video_is_rejected_for_para_event(): void
    {
        $video = $this->createVideo(
            title: "Women's Speed qualification || Salt Lake City 2026",
            duration: 85,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: '2026-05-21T12:00:00Z',
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags('Paraclimbing Qualification'),
            event: $this->createEvent(
                eventName: 'IFSC Para Climbing World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
            ),
        );

        $this->assertNull($score);
    }

    #[Test] public function paraclimbing_semi_final_is_scored(): void
    {
        $video = $this->createVideo(
            title: 'Para Climbing semi-final | Salt Lake City 2026',
            duration: 120,
            publishedAt: '2026-05-21T11:00:00Z',
            scheduledStartTime: '2026-05-21T12:00:00Z',
        );

        $score = $this->matchScorer->score(
            video: $video,
            roundTags: $this->roundTags('Paraclimbing Semi-Final'),
            event: $this->createEvent(
                eventName: 'IFSC Para Climbing World Cup Salt Lake City 2026',
                location: 'Salt Lake City',
            ),
        );

        $this->assertNotNull($score);
    }

    /** @return Tag[] */
    private function roundTags(string $roundName): array
    {
        return $this->tagsParser->fromString(mb_strtolower($roundName))->allTags();
    }

    private function createEvent(
        string $eventName,
        string $location,
        string $localStartDate = '2026-05-20T08:00:00Z',
        string $localEndDate = '2026-05-22T20:00:00Z',
    ): IFSCEventInfo {
        return new IFSCEventInfo(
            eventId: 9000,
            eventName: $eventName,
            slug: 'ifsc-world-cup',
            leagueId: 37,
            leagueName: 'World Cups and World Championships',
            leagueSeasonId: 99,
            localStartDate: $localStartDate,
            localEndDate: $localEndDate,
            timeZone: new DateTimeZone('Europe/Madrid'),
            location: $location,
            country: 'USA',
            disciplines: [],
            categories: [],
        );
    }

    private function createVideo(
        string $title,
        int $duration,
        string $publishedAt,
        ?string $scheduledStartTime,
    ): YouTubeVideo {
        return new YouTubeVideo(
            title: $title,
            duration: $duration,
            videoId: 'video-id',
            publishedAt: new DateTimeImmutable($publishedAt),
            scheduledStartTime: $scheduledStartTime ? new DateTimeImmutable($scheduledStartTime) : null,
            restrictedRegions: [],
        );
    }

    protected function setUp(): void
    {
        $this->tagsParser = new IFSCTagsParser();
        $this->matchScorer = new YouTubeMatchScorer(
            tagsParser: $this->tagsParser,
            textNormalizer: new YouTubeTextNormalizer(),
        );
    }
}
