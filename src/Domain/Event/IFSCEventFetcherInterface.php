<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;

interface IFSCEventFetcherInterface
{
    /** @return IFSCEvent[] */
    public function fetchEventsForLeague(int $season, IFSCLeague $league): array;
}
