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
use nicoSWD\IfscCalendar\Domain\Calendar\PostProcess\Season2024PostProcessor;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

final readonly class IFSCCalendarPostProcess
{
    public function __construct(
        private Season2023PostProcessor $season2023PostProcessor,
        private Season2024PostProcessor $season2024PostProcessor,
    ) {
    }

    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     * @throws Exception
     */
    public function process(IFSCSeasonYear $season, array $events): array
    {
        switch ($season) {
            case IFSCSeasonYear::SEASON_2016:
            case IFSCSeasonYear::SEASON_2017:
            case IFSCSeasonYear::SEASON_2018:
            case IFSCSeasonYear::SEASON_2019:
            case IFSCSeasonYear::SEASON_2020:
            case IFSCSeasonYear::SEASON_2021:
            case IFSCSeasonYear::SEASON_2022:
            case IFSCSeasonYear::SEASON_2025:
                break;
            case IFSCSeasonYear::SEASON_2023:
                $events = $this->season2023PostProcessor->process($events);
                break;
            case IFSCSeasonYear::SEASON_2024:
                $events = $this->season2024PostProcessor->process($events);
                break;
        }

       $this->orderEventsByDate($events);

        return $events;
    }

    /** @param IFSCEvent[] $events */
    private function orderEventsByDate(array &$events): void
    {
        usort($events, $this->orderByDate());
    }

    private function orderByDate(): Closure
    {
        return static fn (IFSCEvent $event1, IFSCEvent $event2) => $event1->startsAt <=> $event2->startsAt;
    }
}
