<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Starter\IFSCStarter;

final class IFSCEvent
{
    /** @param IFSCRound[] $rounds */
    /** @param IFSCStarter[] $starters */
    public function __construct(
        public readonly IFSCSeasonYear $season,
        public readonly int $eventId,
        public readonly string $timeZone,
        public readonly string $eventName,
        public readonly string $location,
        public readonly string $country,
        public readonly ?string $poster,
        public readonly string $siteUrl,
        public readonly string $startsAt,
        public readonly string $endsAt,
        public readonly array $disciplines,
        public array $rounds,
        public array $starters = [],
    ) {
    }
}
