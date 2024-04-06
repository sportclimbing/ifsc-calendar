<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\PostProcess\Season2024PostProcessor;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventSorter;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

readonly final class IFSCCalendarPostProcess
{
    public function __construct(
        private IFSCEventSorter $eventSorter,
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
            case IFSCSeasonYear::SEASON_2023:
            case IFSCSeasonYear::SEASON_2025:
                break;
            case IFSCSeasonYear::SEASON_2024:
                $events = $this->season2024PostProcessor->process($events);
                break;
        }

        $this->eventSorter->sortByDate($events);

        return $events;
    }
}
