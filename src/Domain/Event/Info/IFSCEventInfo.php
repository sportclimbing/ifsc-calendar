<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Info;

use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;

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
        public int $leagueSeasonId,
        public string $timeZone,
        public string $location,
        public string $country,
        public array $disciplines,
        public array $categories,
    ) {
    }
}
