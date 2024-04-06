<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

interface IFSCEventFetcherInterface
{
    /**
     * @param string[] $selectedLeagues
     * @return IFSCEvent[]
     */
    public function fetchEventsForSeason(IFSCSeasonYear $season, array $selectedLeagues): array;
}
