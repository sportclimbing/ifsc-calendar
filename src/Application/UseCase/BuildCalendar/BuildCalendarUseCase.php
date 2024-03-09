<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar;

use nicoSWD\IfscCalendar\Domain\Calendar\Exceptions\NoEventsFoundException;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarBuilder;

final readonly class BuildCalendarUseCase
{
    public function __construct(
        private IFSCCalendarBuilder $calendarBuilder,
    ) {
    }

    /** @throws NoEventsFoundException */
    public function execute(BuildCalendarRequest $buildCalendarRequest): BuildCalendarResponse
    {
        return new BuildCalendarResponse(
            calendarContents: $this->buildCalendar($buildCalendarRequest),
        );
    }

    /** @throws NoEventsFoundException */
    public function buildCalendar(BuildCalendarRequest $buildCalendarRequest): string
    {
        return $this->calendarBuilder->generateForLeague(
            season: $buildCalendarRequest->season,
            leagueIds: $buildCalendarRequest->leagueIds,
            format: $buildCalendarRequest->format,
        );
    }
}
