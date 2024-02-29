<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Closure;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\PostProcess\Season2023PostProcessor;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCCalendarPostProcess
{
    public function __construct(
        private Season2023PostProcessor $season2023PostProcessor,
    ) {
    }

    /**
     * @param int $season
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     * @throws Exception
     */
    public function process(int $season, array $events): array
    {
        switch ($season) {
            case 2023:
                $events = $this->season2023PostProcessor->process($events);
                break;
        }

       $this->orderEvents($events);

        return $events;
    }

    /** @param IFSCEvent[] $events */
    private function orderEvents(array &$events): void
    {
        usort($events, $this->orderByDate());
    }

    private function orderByDate(): Closure
    {
        return static fn (IFSCEvent $event1, IFSCEvent $event2) => $event1->startsAt <=> $event2->startsAt;
    }
}
