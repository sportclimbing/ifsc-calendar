<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Event;

use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;

interface IFSCEventFetcherInterface
{
    /**
     * @param string[] $selectedLeagues
     * @return IFSCEvent[]
     */
    public function fetchEventsForSeason(IFSCSeasonYear $season, array $selectedLeagues, string $schedulePath): array;
}
