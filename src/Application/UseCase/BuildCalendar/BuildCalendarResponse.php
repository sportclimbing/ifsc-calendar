<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar;

final readonly class BuildCalendarResponse
{
    /** @param string[] $calendarContents */
    public function __construct(
        public array $calendarContents,
    ) {
    }
}
