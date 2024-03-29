<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Schedule;

interface IFSCScheduleProvider
{
    /** @return IFSCSchedule[] */
    public function parseSchedule(string $html, string $timeZone): array;
}
