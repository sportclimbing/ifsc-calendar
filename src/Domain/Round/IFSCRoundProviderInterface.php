<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;

interface IFSCRoundProviderInterface
{
    /** @return IFSCSchedule[] */
    public function fetchRounds(object $event): array;
}
