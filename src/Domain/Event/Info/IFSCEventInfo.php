<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Info;

use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCEventInfo
{
    /**
     * @param IFSCDiscipline[] $disciplines
     * @param IFSCEventCategory[] $categories
     */
    public function __construct(
        public int $eventId,
        public string $eventName,
        public int $leagueId,
        public string $leagueName,
        public int $leagueSeasonId,
        public string $localStartDate,
        public string $localEndDate,
        public DateTimeZone $timeZone,
        public string $location,
        public string $country,
        public array $disciplines,
        public array $categories,
    ) {
    }

    public static function fromEvent(IFSCEvent $event): self
    {
        return new self(
            eventId: $event->eventId,
            eventName: $event->eventName,
            leagueId: 0,
            leagueName: $event->leagueName,
            leagueSeasonId: 0,
            localStartDate: '',
            localEndDate: '',
            timeZone: $event->timeZone,
            location: $event->location,
            country: $event->country,
            disciplines: $event->disciplines,
            categories: [],
        );
    }
}
