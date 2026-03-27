<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Application\UseCase\BuildCalendar;

use SportClimbing\IfscCalendar\Domain\Calendar\IFSCCalendarBuilder;
use SportClimbing\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;

final readonly class BuildCalendarUseCase
{
    public function __construct(
        private IFSCCalendarBuilder $calendarBuilder,
    ) {
    }

    /** @throws InvalidURLException */
    public function execute(BuildCalendarRequest $buildCalendarRequest): BuildCalendarResponse
    {
        return new BuildCalendarResponse(
            calendarContents: $this->buildCalendar($buildCalendarRequest),
        );
    }

    /**
     * @return array<string,string>
     * @throws InvalidURLException
     */
    private function buildCalendar(BuildCalendarRequest $buildCalendarRequest): array
    {
        return $this->calendarBuilder->generateForSeason(
            season: $buildCalendarRequest->season,
            leagues: $buildCalendarRequest->leagues,
            formats: $buildCalendarRequest->formats,
            schedulePath: $buildCalendarRequest->schedulePath,
        );
    }
}
