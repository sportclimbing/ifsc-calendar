<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

final readonly class YouTubeVideoProvider
{
    public function __construct(
        private YouTubeApiClient $apiClient,
    ) {
    }

    public function fetchAllVideos(): YouTubeVideoCollection
    {
        return $this->apiClient->fetchAllVideos();
    }
}
