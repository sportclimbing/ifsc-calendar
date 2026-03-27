<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Application\UseCase\BuildCalendar;

use SportClimbing\IfscCalendar\Domain\Calendar\IFSCCalendarFormat;
use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;

final readonly class BuildCalendarRequest
{
    /**
     * @param string[] $leagues
     * @param IFSCCalendarFormat[] $formats
     */
    public function __construct(
        public IFSCSeasonYear $season,
        public array $leagues,
        public array $formats,
        public string $schedulePath,
    ) {
    }
}
