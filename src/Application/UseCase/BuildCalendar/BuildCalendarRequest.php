<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar;

use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarFormat;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

final readonly class BuildCalendarRequest
{
    /** @param int[] $leagueIds */
    public function __construct(
        public array $leagueIds,
        public IFSCSeasonYear $season,
        public IFSCCalendarFormat $format,
    ) {
    }
}
