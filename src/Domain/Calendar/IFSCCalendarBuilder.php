<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;

final readonly class IFSCCalendarBuilder
{
    public function __construct(
        private CalendarGeneratorInterface $calendarGenerator,
        private IFSCEventFetcherInterface $eventFetcher,
    ) {
    }

    /**
     * @param int $season
     * @var IFSCLeague[] $leagues
     */
    public function generateForLeagues(int $season, array $leagues): string
    {
        $events = [];

        foreach ($leagues as $league) {
            $events += $this->eventFetcher->fetchEventsForLeague($season, $league);
        }

        return $this->calendarGenerator->generateForEvents($events);
    }
}
