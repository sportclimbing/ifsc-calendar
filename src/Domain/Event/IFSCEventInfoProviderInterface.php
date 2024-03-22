<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;

interface IFSCEventInfoProviderInterface
{
    /** @throws IFSCApiClientException */
    public function fetchEventInfo(int $eventId): IFSCEventInfo;

    /** @throws IFSCApiClientException */
    public function fetchEventsForSeason(IFSCSeasonYear $season): array;

    /** @throws IFSCApiClientException */
    public function fetchLeagueNameById(int $leagueId): string;

    /**
     * @throws IFSCApiClientException
     * @return IFSCSeason[]
     */
    public function fetchSeasons(): array;
}
