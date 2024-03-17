<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;

interface IFSCEventInfoProviderInterface
{
    /** @throws IFSCApiClientException */
    public function fetchInfo(int $eventId): object;

    /** @throws IFSCApiClientException */
    public function fetchEventsForLeague(int $leagueId): array;

    /** @throws IFSCApiClientException */
    public function fetchLeagueNameById(int $leagueId): string;

    /**
     * @throws IFSCApiClientException
     * @return IFSCSeason[]
     */
    public function fetchSeasons(): array;
}
