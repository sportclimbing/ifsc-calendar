<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

interface YouTubeApiClient
{
    public function fetchRecentVideos(IFSCSeasonYear $season): YouTubeVideoCollection;
}
