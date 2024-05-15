<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\PostProcess;

use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundProviderInterface;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;

final readonly class Season2024PostProcessor
{
    private const int WUJIANG_IFSC_EVENT_ID = 1354;
    private const string WUJIAN_POSTER_URL = 'https://res.dasheng.top/227/pro/ctms_tool/20240323/jpg/d7f6d496a71d4dc785c8d0e51169d17b.jpg';
    private const int OLYMPIC_QUALIFIERS_SHANGHAI_ID = 1384;
    private const string OLYMPIC_QUALIFIERS_SHANGHAI_INFO_SHEET = 'https://images.ifsc-climbing.org/ifsc/image/private/t_q_good/prd/yg3cqznmay12orsv9hpa.pdf';
    private const string OLYMPIC_QUALIFIERS_SHANGHAI_LIVE_STREAM = 'https://olympics.com/en/sport-events/olympic-qualifier-series-2024-shanghai/broadcasting-schedule';

    public function __construct(
        private IFSCRoundProviderInterface $roundProvider,
        private IFSCRoundFactory $roundFactory,
    ) {
    }
    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     * @throws Exception
     */
    public function process(array $events): array
    {
        foreach ($events as $event) {
            if ($this->isWujiangEvent($event)) {
                $event->poster = self::WUJIAN_POSTER_URL;
            } elseif ($this->isOlympicQualifiersShanghai($event)) {
               $event->rounds = $this->fetchOlympicQualifiersShanghaiRounds($event);
            }
        }

        return $events;
    }

    private function isWujiangEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::WUJIANG_IFSC_EVENT_ID;
    }

    private function isOlympicQualifiersShanghai(IFSCEvent $event): bool
    {
        return $event->eventId === self::OLYMPIC_QUALIFIERS_SHANGHAI_ID;
    }

    private function fetchOlympicQualifiersShanghaiRounds(IFSCEvent $event): array
    {
        $rounds = [];
        $eventInfo = IFSCEventInfo::fromEvent($event);
        /** @var IFSCSchedule[] $schedules */
        $schedules = $this->roundProvider->fetchRoundsFromInfoSheet(
            event: $eventInfo,
            infoSheetUrl: self::OLYMPIC_QUALIFIERS_SHANGHAI_INFO_SHEET,
        );

        foreach ($schedules as $schedule) {
            $rounds[] = $this->roundFactory->create(
                event: $eventInfo,
                roundName: $schedule->name,
                startTime: $schedule->startsAt,
                endTime: $schedule->endsAt,
                status: IFSCRoundStatus::PROVISIONAL,
                liveStream: new LiveStream(self::OLYMPIC_QUALIFIERS_SHANGHAI_LIVE_STREAM),
            );
        }

        return $rounds;
    }
}
