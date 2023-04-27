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
        private IFSCCalendarBuilderFactory $calendarBuilderFactory,
        private IFSCEventFetcherInterface $eventFetcher,
    ) {
    }

    /**
     * @param int $season
     * @param IFSCLeague[] $leagues
     * @param string $format
     * @return string
     */
    public function generateForLeagues(int $season, array $leagues, string $format): string
    {
        $events = [];

        foreach ($leagues as $league) {
            $events += $this->eventFetcher->fetchEventsForLeague($season, $league);
        }

        return $this->calendarBuilderFactory->generateForFormat($format, $events);
    }
}
