<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\StartList;

interface IFSCStartListProviderInterface
{
    /** @return IFSCStarter[] */
    public function fetchStartListForEvent(int $eventId): array;
}
