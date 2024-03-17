<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;

interface IFSCEventInfoProviderInterface
{
    public function fetchInfo(int $eventId): object;

    public function fetchEventsForLeague(int $leagueId): array;

    public function fetchLeagueNameById(int $leagueId): string;

    /** @return IFSCSeason[] */
    public function fetchSeasons(): array;
}
