<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Event\Info;

use DateTimeZone;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCEventInfo
{
    /**
     * @param IFSCDiscipline[] $disciplines
     * @param IFSCEventCategory[] $categories
     */
    public function __construct(
        public int $eventId,
        public string $eventName,
        public string $slug,
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
        public ?string $infosheetUrl = null,
        public ?string $ticketsSummary = null,
        public ?string $ticketsPurchaseUrl = null,
    ) {
    }
}
