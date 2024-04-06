<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\YouTube;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeApiClient;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideo;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideoCollection;
use nicoSWD\IfscVideos\Domain\YouTube\YouTubeVideo as IfscYouTubeVideo;
use nicoSWD\IfscVideos\Domain\YouTube\YouTubeVideoCollection as IfscYouTubeVideoCollection;
use Override;

readonly final class YouTubeVideoProvider implements YouTubeApiClient
{
    public function __construct(
        private IfscYouTubeVideoCollection $youTubeVideoCollection,
    ) {
    }

    #[Override] public function fetchAllVideos(): YouTubeVideoCollection
    {
        $videoCollection = new YouTubeVideoCollection();

        foreach ($this->youTubeVideoCollection->getAllVideos() as $video) {
            $videoCollection->add($this->createVideo($video));
        }

        return $videoCollection;
    }

    #[Override] public function fetchRecentVideos(IFSCSeasonYear $season): YouTubeVideoCollection
    {
        $videoCollection = new YouTubeVideoCollection();

        foreach ($this->fetchLatestVideos($season) as $video) {
            $videoCollection->add($this->createVideo($video));
        }

        return $videoCollection;
    }

    /** @inheritdoc  */
    #[Override] public function fetchRestrictedRegionsForVideo(string $videoId): array
    {
        return $this->youTubeVideoCollection->fetchRestrictedRegionsForVideo($videoId);
    }

    /** @return IfscYouTubeVideo[] */
    private function fetchLatestVideos(IFSCSeasonYear $season): array
    {
        return $this->youTubeVideoCollection->getVideosForSeason($season->value);
    }

    private function createVideo(IfscYouTubeVideo $video): YouTubeVideo
    {
        return new YouTubeVideo(
            title: $video->title,
            duration: $video->duration,
            videoId: $video->videoId,
            publishedAt: $video->publishedAt,
            scheduledStartTime: $video->scheduledStartTime,
            restrictedRegions: $video->restrictedRegions,
        );
    }
}
