<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Calendar;

use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;

interface IFSCCalendarGeneratorInterface
{
    /** @param IFSCEvent[] $events */
    public function generateForEvents(array $events): string;
}
