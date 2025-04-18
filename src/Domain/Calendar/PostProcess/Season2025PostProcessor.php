<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
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

final readonly class Season2025PostProcessor
{
    private const int KEQIAO_IFSC_EVENT_ID = 1405;

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
            if ($this->isKeqiaoEvent($event)) {
               $event->rounds = $this->fetchKeqiaoRounds($event);
            }
        }

        return $events;
    }

    private function isKeqiaoEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::KEQIAO_IFSC_EVENT_ID;
    }

    /** @return IFSCRound[] */
    private function fetchKeqiaoRounds(IFSCEvent $event): array
    {
        $eventInfo = IFSCEventInfo::fromEvent($event);

        return [
            // 18/4
            $this->keqiaoRound($eventInfo, "Women's Boulder Qualification", '2025-04-18T09:00:00+08:00'),
            $this->keqiaoRound($eventInfo, "Men's Boulder Qualification", '2025-04-18T16:00:00+08:00'),

            // 19/4
            $this->keqiaoRound($eventInfo, "Women's Boulder Semi-Final", '2025-04-19T12:00:00+08:00', 'https://youtu.be/-CSZqzed9PQ'),
            $this->keqiaoRound($eventInfo, "Men's Boulder Semi-Final", '2025-04-19T19:00:00+08:00', 'https://youtu.be/tvubZ1KsX50'),

            // 20/4
            $this->keqiaoRound($eventInfo, "Women's Boulder Final", '2025-04-20T12:00:00+08:00', 'https://youtu.be/kr-MYvpn7zM'),
            $this->keqiaoRound($eventInfo, "Men's Boulder Final", '2025-04-20T19:00:00+08:00', 'https://youtu.be/eLdRhRqQ2D0'),
        ];
    }

    private function keqiaoRound(IFSCEventInfo $eventInfo, string $title, string $startsAt, string $streamUrl = ''): IFSCRound
    {
        return $this->round($eventInfo, $title, $startsAt, 'Asia/Shanghai', $streamUrl);
    }

    private function round(IFSCEventInfo $eventInfo, string $title, string $startsAt, string $timeZone, ?string $streamUrl): IFSCRound
    {
        $schedule = $this->scheduleFactory->create(
            name: $title,
            startsAt: (new DateTimeImmutable($startsAt))->setTimezone(new DateTimeZone($timeZone)),
            endsAt: null,
        );

        $liveStream = new LiveStream($streamUrl);

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
