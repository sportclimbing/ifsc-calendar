<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

enum IFSCCalendarFormat: string
{
    case FORMAT_JSON = 'json';
    case FORMAT_ICS = 'ics';
}
