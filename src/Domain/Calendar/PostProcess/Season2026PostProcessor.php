<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Calendar\PostProcess;

use Exception;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundFactory;
use SportClimbing\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;

final readonly class Season2026PostProcessor
{
    public function __construct(
        private IFSCRoundFactory $roundFactory,
        private IFSCScheduleFactory $scheduleFactory,
    ) {
    }
    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     * @throws Exception
     */
    public function process(array $events): array
    {
        return $events;
    }
}
