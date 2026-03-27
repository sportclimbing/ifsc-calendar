<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Event;

use Closure;

final class IFSCEventSorter
{
    /** @param IFSCEvent[] $events */
    public function sortByDate(array &$events): void
    {
        usort($events, $this->sortEventsByDate());
    }

    private function sortEventsByDate(): Closure
    {
        return static function (IFSCEvent $event1, IFSCEvent $event2): int {
            return $event1->startsAt <=> $event2->startsAt;
        };
    }
}
