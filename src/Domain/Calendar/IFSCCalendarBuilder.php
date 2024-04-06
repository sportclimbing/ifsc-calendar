<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Exception;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

readonly final class IFSCCalendarBuilder
{
    public function __construct(
        private IFSCCalendarBuilderFactory $calendarBuilderFactory,
        private IFSCEventFetcherInterface $eventFetcher,
        private IFSCCalendarPostProcess $calendarPostProcess,
    ) {
    }

    /**
     * @param string[] $leagues
     * @param IFSCCalendarFormat[] $formats
     * @throws InvalidURLException
     * @throws Exception
     * @return array<string,string>
     */
    public function generateForSeason(IFSCSeasonYear $season, array $leagues, array $formats): array
    {
        $events = $this->calendarPostProcess->process(
            season: $season,
            events: $this->fetchEvents($season, $leagues),
        );

        return $this->buildCalendars($formats, $events);
    }

    /**
     * @param string[] $leagues
     * @return IFSCEvent[]
     */
    private function fetchEvents(IFSCSeasonYear $season, array $leagues): array
    {
        return $this->eventFetcher->fetchEventsForSeason($season, $leagues);
    }

    /**
     * @param IFSCCalendarFormat[] $formats
     * @param IFSCEvent[] $events
     * @return array<string,string>
     */
    private function buildCalendars(array $formats, array $events): array
    {
        $results = [];

        foreach ($formats as $format) {
            $results[$format->value] = $this->calendarBuilderFactory->generateForFormat($format, $events);
        }

        return $results;
    }
}
