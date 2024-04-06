<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\PostProcess;

use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

readonly final class Season2024PostProcessor
{
    private const int WUJIANG_IFSC_EVENT_ID = 1354;

    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     * @throws Exception
     */
    public function process(array $events): array
    {
        foreach ($events as $event) {
            if ($this->isWujiangEvent($event)) {
                $event->poster = 'https://res.dasheng.top/227/pro/ctms_tool/20240323/jpg/d7f6d496a71d4dc785c8d0e51169d17b.jpg';
            }
        }

        return $events;
    }

    private function isWujiangEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::WUJIANG_IFSC_EVENT_ID;
    }
}
