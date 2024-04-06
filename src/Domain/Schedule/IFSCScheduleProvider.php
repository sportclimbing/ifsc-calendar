<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Schedule;

use DateTimeZone;

interface IFSCScheduleProvider
{
    /** @return IFSCSchedule[] */
    public function parseSchedule(string $html, DateTimeZone $timeZone): array;
}
