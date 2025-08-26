<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use Closure;

final class IFSCEventSorter
{
    /** @param IFSCEvent[] $events */
    public function sortByDate(array &$events): void
    {
        $events = array_filter($events, $this->eventHasRounds());

        usort($events, $this->sortEventsByDate());
    }

    private function sortEventsByDate(): Closure
    {
        return static function (IFSCEvent $event1, IFSCEvent $event2): int {
            return $event1->rounds[0]->startTime <=> $event2->rounds[0]->startTime;
        };
    }

    private function eventHasRounds(): Closure
    {
        return static fn (IFSCEvent $event): bool => count($event->rounds) > 0;
    }
}
