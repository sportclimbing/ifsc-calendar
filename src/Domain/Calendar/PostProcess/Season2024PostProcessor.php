<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\PostProcess;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;

final readonly class Season2024PostProcessor
{
    private const int WUJIANG_IFSC_EVENT_ID = 1354;
    private const string WUJIAN_POSTER_URL = 'https://res.dasheng.top/227/pro/ctms_tool/20240323/jpg/d7f6d496a71d4dc785c8d0e51169d17b.jpg';
    private const int OLYMPIC_QUALIFIERS_SHANGHAI_ID = 1384;
    private const string OLYMPIC_QUALIFIERS_SHANGHAI_LIVE_STREAM = 'https://olympics.com/en/sport-events/olympic-qualifier-series-2024-shanghai/broadcasting-schedule';

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
        $eventInfo = IFSCEventInfo::fromEvent($event);

        return [
            // 16/5
            $this->round($eventInfo, "Men's & Women's Boulder Qualification", '2024-05-16T10:30:00+08:00'),

            // 17/5
            $this->round($eventInfo, "Men's & Women's Lead Qualification", '2024-05-17T10:00:00+08:00'),
            $this->round($eventInfo, "Women's Speed Qualification", '2024-05-17T16:50:00+08:00'),
            $this->round($eventInfo, "Men's Speed Qualification", '2024-05-17T17:45:00+08:00'),

            // 18/5
            $this->round($eventInfo, "Men's & Women's Boulder Semifinal", '2024-05-18T09:30:00+08:00'),
            $this->round($eventInfo, "Men's & Women's Lead Semifinal", '2024-05-18T13:30:00+08:00'),
            $this->round($eventInfo, "Men's & Women's Speed Final", '2024-05-18T17:00:00+08:00'),

            // 19/05
            $this->round($eventInfo, "Men's Boulder Final", '2024-05-19T10:00:00+08:00'),
            $this->round($eventInfo, "Men's Lead Final", '2024-05-19T12:05:00+08:00'),
            $this->round($eventInfo, "Women's Boulder Final", '2024-05-19T15:25:00+08:00'),
            $this->round($eventInfo, "Women's Lead Final", '2024-05-19T17:30:00+08:00'),
        ];
    }

    private function round(IFSCEventInfo $eventInfo, string $title, string $startsAt): IFSCRound
    {
        $schedule = $this->scheduleFactory->create(
            name: $title,
            startsAt: (new DateTimeImmutable($startsAt))->setTimezone(new DateTimeZone('Asia/Shanghai')),
            endsAt: null,
        );

        $liveStream = new LiveStream(self::OLYMPIC_QUALIFIERS_SHANGHAI_LIVE_STREAM);

        return $this->roundFactory->create(
            event: $eventInfo,
            roundName: $schedule->name,
            startTime: $schedule->startsAt,
            endTime: $schedule->endsAt,
            status: IFSCRoundStatus::CONFIRMED,
            liveStream: $liveStream,
        );
    }
}
