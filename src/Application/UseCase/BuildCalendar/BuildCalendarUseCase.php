<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar;

use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarBuilder;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;

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

    /** @throws InvalidURLException */
    private function buildCalendar(BuildCalendarRequest $buildCalendarRequest): array
    {
        return $this->calendarBuilder->generateForLeague(
            season: $buildCalendarRequest->season,
            formats: $buildCalendarRequest->formats,
        );
    }
}
