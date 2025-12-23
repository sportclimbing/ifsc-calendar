<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeImmutable;
use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStarter;

final class IFSCEvent
{
    /** @var IFSCRound[] $rounds */
    public array $rounds;

    /**
     * @param IFSCRound[] $rounds
     * @param IFSCStarter[] $startList
     * @param IFSCDiscipline[] $disciplines
     */
    public function __construct(
        public readonly IFSCSeasonYear $season,
        public readonly int $eventId,
        public readonly string $slug,
        public readonly string $leagueName,
        public readonly DateTimeZone $timeZone,
        public readonly string $eventName,
        public readonly string $location,
        public readonly string $country,
        public ?string $poster,
        public readonly string $siteUrl,
        public readonly DateTimeImmutable $startsAt,
        public readonly DateTimeImmutable $endsAt,
        public readonly array $disciplines,
        array $rounds,
        public readonly array $startList = [],
    ) {
        $this->rounds = $rounds;
    }
}
