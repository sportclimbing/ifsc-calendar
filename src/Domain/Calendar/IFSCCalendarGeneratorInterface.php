<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

interface IFSCCalendarGeneratorInterface
{
    /** @param IFSCEvent[] $events */
    public function generateForEvents(array $events): string;
}
