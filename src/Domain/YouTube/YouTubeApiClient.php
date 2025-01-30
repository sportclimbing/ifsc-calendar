<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

interface YouTubeApiClient
{
    public function fetchAllVideos(): YouTubeVideoCollection;

    public function fetchRecentVideos(IFSCSeasonYear $season): YouTubeVideoCollection;

    /** @return string[] */
    public function fetchRestrictedRegionsForVideo(string $videoId): array;
}
