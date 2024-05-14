<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Schedule;

use Iterator;
use Override;

final readonly class ThreeColumnInfoSheetScheduleParser extends InfoSheetScheduleParser
{
    #[Override] protected function parse(string $schedule): Iterator
    {
        $scheduleRegex = '~
            (?<start_time>\d?\d:\d\d|follow(?:ing|ed)\s+by)\n?(?:\s*-\s*
            (?<end_time>\d?\d:\d\d))?\s*\n
            (?<name>[^\r\n]+)\s*\n~xi';

        if (preg_match_all($scheduleRegex, $schedule, $match, flags: PREG_UNMATCHED_AS_NULL)) {
            foreach (array_keys($match['start_time']) as $key) {
                yield [
                    'name' => $match['name'][$key],
                    'start_time' => $match['start_time'][$key],
                    'end_time' => $match['end_time'][$key],
                ];
            }
        }
    }
}
