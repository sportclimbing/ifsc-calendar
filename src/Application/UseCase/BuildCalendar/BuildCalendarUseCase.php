<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar;

use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarBuilder;

final readonly class BuildCalendarUseCase
{
    public function __construct(
        private IFSCCalendarBuilder $calendarBuilder,
    ) {
    }

    public function execute(BuildCalendarRequest $buildCalendarRequest): BuildCalendarResponse
    {
        return new BuildCalendarResponse(
            calendarContents: $this->buildCalendar($buildCalendarRequest),
        );
    }

    public function buildCalendar(BuildCalendarRequest $buildCalendarRequest): string
    {
        return $this->calendarBuilder->generateForLeague(
            season: $buildCalendarRequest->season,
            league: $buildCalendarRequest->league,
            format: $buildCalendarRequest->format,
            fetchYouTubeUrls: $buildCalendarRequest->fetchYouTubeUrls,
        );
    }
}
