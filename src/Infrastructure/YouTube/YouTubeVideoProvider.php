<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\YouTube;

use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeApiClient;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideo;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideoCollection;
use nicoSWD\IfscVideos\Domain\YouTube\YouTubeVideo as IfscYouTubeVideo;
use nicoSWD\IfscVideos\Domain\YouTube\YouTubeVideoCollection as IfscYouTubeVideoCollection;

final readonly class YouTubeVideoProvider implements YouTubeApiClient
{
    public function __construct(
        private IfscYouTubeVideoCollection $youTubeVideoCollection,
    ) {
    }

    public function fetchRecentVideos(int $season): YouTubeVideoCollection
    {
        $videoCollection = new YouTubeVideoCollection();

        foreach ($this->fetchLatestVideos($season) as $video) {
            $videoCollection->add($this->createVideo($video));
        }

        return $videoCollection;
    }

    /** @return IfscYouTubeVideo[] */
    private function fetchLatestVideos(int $season): array
    {
        return $this->youTubeVideoCollection->getVideosForSeason($season);
    }

    public function createVideo(IfscYouTubeVideo $item): YouTubeVideo
    {
        return new YouTubeVideo(
            title: $item->title,
            duration: $item->duration,
            videoId: $item->videoId,
            publishedAt: $item->publishedAt,
        );
    }
}
