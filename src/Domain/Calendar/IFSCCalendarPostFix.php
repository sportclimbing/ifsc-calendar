<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use nicoSWD\IfscCalendar\Domain\Calendar\Fixes\SeasonFix2023;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCCalendarPostFix
{
    public function __construct(
        private SeasonFix2023 $seasonFix2023,
    ) {
    }

    /**
     * @param int $season
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     */
    public function fix(int $season, array $events): array
    {
        switch ($season) {
            case 2023:
                $events = $this->seasonFix2023->fix($events);
                break;
        }

        return $events;
    }
}
