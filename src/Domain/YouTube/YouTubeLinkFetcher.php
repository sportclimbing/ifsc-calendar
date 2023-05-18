<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

final readonly class YouTubeLinkFetcher
{
    public function __construct(
        private YouTubeApiClient $api,
    ) {
    }

    public function fetchRecentVideos(int $season): YouTubeVideoCollection
    {
        return $this->api->fetchRecentVideos($season);
    }
}
