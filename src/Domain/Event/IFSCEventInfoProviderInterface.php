<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;

interface IFSCEventInfoProviderInterface
{
    /**
     * @param IFSCLeague[] $leagues
     * @return IFSCEventInfo[]
     * @throws IFSCApiClientException
     */
    public function fetchEventsForLeagues(array $leagues): array;

    /**
     * @throws IFSCApiClientException
     * @return IFSCSeason[]
     */
    public function fetchSeasons(): array;
}
