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
    private const int OLYMPIC_QUALIFIERS_BUDAPEST_ID = 1385;
    private const string OLYMPIC_QUALIFIERS_BUDAPEST_LIVE_STREAM = 'https://olympics.com/en/sport-events/olympic-qualifier-series-2024-budapest/broadcasting-schedule';
    private const int CHAMONIX_IFSC_EVENT_ID = 1357;
    private const int BRIANCON_IFSC_EVENT_ID = 1358;
    private const int OLYMPICS_EVENT_ID = 1386;


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
            } elseif ($this->isOlympicQualifiersBudapest($event)) {
               $event->rounds = $this->fetchOlympicQualifiersBudapestRounds($event);
            } elseif ($this->isChamonixEvent($event)) {
               $event->rounds = $this->fetchChamonixRounds($event);
            } elseif ($this->isBrianconEvent($event)) {
               $event->rounds = $this->fetchBrianconRounds($event);
            } elseif ($this->isOlympicsEvent($event)) {
               $event->rounds = $this->fetchOlympicsRounds($event);
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

    private function isOlympicQualifiersBudapest(IFSCEvent $event): bool
    {
        return $event->eventId === self::OLYMPIC_QUALIFIERS_BUDAPEST_ID;
    }

    private function isChamonixEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::CHAMONIX_IFSC_EVENT_ID;
    }

    private function isBrianconEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::BRIANCON_IFSC_EVENT_ID;
    }

    private function isOlympicsEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::OLYMPICS_EVENT_ID;
    }

    private function fetchOlympicQualifiersShanghaiRounds(IFSCEvent $event): array
    {
        $eventInfo = IFSCEventInfo::fromEvent($event);

        return [
            // 16/5
            $this->shanghaiRound($eventInfo, "Men's & Women's Boulder Qualification", '2024-05-16T10:30:00+08:00'),

            // 17/5
            $this->shanghaiRound($eventInfo, "Men's & Women's Lead Qualification", '2024-05-17T10:00:00+08:00'),
            $this->shanghaiRound($eventInfo, "Women's Speed Qualification", '2024-05-17T16:50:00+08:00'),
            $this->shanghaiRound($eventInfo, "Men's Speed Qualification", '2024-05-17T17:45:00+08:00'),

            // 18/5
            $this->shanghaiRound($eventInfo, "Men's & Women's Boulder Semifinal", '2024-05-18T09:30:00+08:00'),
            $this->shanghaiRound($eventInfo, "Men's & Women's Lead Semifinal", '2024-05-18T13:30:00+08:00'),
            $this->shanghaiRound($eventInfo, "Men's & Women's Speed Final", '2024-05-18T17:00:00+08:00'),

            // 19/05
            $this->shanghaiRound($eventInfo, "Men's Boulder Final", '2024-05-19T10:00:00+08:00'),
            $this->shanghaiRound($eventInfo, "Men's Lead Final", '2024-05-19T12:05:00+08:00'),
            $this->shanghaiRound($eventInfo, "Women's Boulder Final", '2024-05-19T15:25:00+08:00'),
            $this->shanghaiRound($eventInfo, "Women's Lead Final", '2024-05-19T17:30:00+08:00'),
        ];
    }

    private function fetchOlympicQualifiersBudapestRounds(IFSCEvent $event): array
    {
        $eventInfo = IFSCEventInfo::fromEvent($event);

        return [
            // 20/6
            $this->budapestRound($eventInfo, "Men's & Women's Boulder Qualification", '2024-06-20T12:00:00+02:00'),

            // 21/6
            $this->budapestRound($eventInfo, "Men's & Women's Lead Qualification", '2024-06-21T10:00:00+02:00'),
            $this->budapestRound($eventInfo, "Women's Speed Qualification", '2024-06-21T17:00:00+02:00'),
            $this->budapestRound($eventInfo, "Men's Speed Qualification", '2024-06-21T17:55:00+02:00'),

            // 22/6
            $this->budapestRound($eventInfo, "Men's & Women's Boulder Semifinal", '2024-06-22T10:00:00+02:00'),
            $this->budapestRound($eventInfo, "Men's & Women's Lead Semifinal", '2024-06-22T16:00:00+02:00'),
            $this->budapestRound($eventInfo, "Men's & Women's Speed Final", '2024-06-22T18:45:00+02:00'),

            // 23/6
            $this->budapestRound($eventInfo, "Women's Boulder Final", '2024-06-23T10:00:00+02:00'),
            $this->budapestRound($eventInfo, "Women's Lead Final", '2024-06-23T12:05:00+02:00'),
            $this->budapestRound($eventInfo, "Men's Boulder Final", '2024-06-23T15:30:00+02:00'),
            $this->budapestRound($eventInfo, "Men's Lead Final", '2024-06-23T17:35:00+02:00'),
        ];
    }

    /** @return IFSCRound[] */
    private function fetchChamonixRounds(IFSCEvent $event): array
    {
        $eventInfo = IFSCEventInfo::fromEvent($event);

        return [
            // 13/07
            $this->chamonixRound($eventInfo, "Men's & Women's Lead Qualification", '2024-07-13T09:00:00+02:00'),
            $this->chamonixRound($eventInfo, "Men's & Women's Speed Qualification", '2024-07-13T18:45:00+02:00', 'https://youtu.be/X-jMhtf_svQ'),
            $this->chamonixRound($eventInfo, "Men's & Women's Speed Final", '2024-07-13T20:55:00+02:00', 'https://youtu.be/EZkv2RIQe94'),

            // 14/07
            $this->chamonixRound($eventInfo, "Men's & Women's Lead Semi-Final", '2024-07-14T10:00:00+02:00', 'https://youtu.be/K7T8E2_cCB0'),
            $this->chamonixRound($eventInfo, "Men's & Women's Lead Final", '2024-07-14T20:30:00+02:00', 'https://youtu.be/UVp79oxI4Uc'),
        ];
    }

    /** @return IFSCRound[] */
    private function fetchBrianconRounds(IFSCEvent $event): array
    {
        $eventInfo = IFSCEventInfo::fromEvent($event);

        return [
            // 17/07
            $this->chamonixRound($eventInfo, "Men's & Women's Speed Qualification", '2024-07-17T12:00:00+02:00', 'https://youtu.be/Nvb0aEkzXpg'),
            $this->chamonixRound($eventInfo, "Men's & Women's Speed Final", '2024-07-17T20:00:00+02:00', 'https://youtu.be/eHn5JkhN6Kg'),

            // 18/07
            $this->chamonixRound($eventInfo, "Men's & Women's Lead Qualification", '2024-07-18T09:00:00+02:00'),
            $this->chamonixRound($eventInfo, "Men's & Women's Lead Semi-Final", '2024-07-18T20:30:00+02:00', 'https://youtu.be/O3XKLAglDZw'),

            // 19/07
            $this->chamonixRound($eventInfo, "Men's & Women's Lead Final", '2024-07-19T20:30:00+02:00', 'https://youtu.be/a9htHC6KagA'),
        ];
    }

    /** @return IFSCRound[] */
    private function fetchOlympicsRounds(IFSCEvent $event): array
    {
        $eventInfo = IFSCEventInfo::fromEvent($event);

        return [
            // 05/08
            $this->chamonixRound($eventInfo, "Men's Boulder Semi-Final", '2024-08-05T10:00:00+02:00', 'https://peacocktv.smart.link/836kod50a'),
            $this->chamonixRound($eventInfo, "Women's Speed Qualification", '2024-08-05T13:00:00+02:00', 'https://peacocktv.smart.link/zgc1owtmz'),

            // 06/08
            $this->chamonixRound($eventInfo, "Women's Boulder Semi-Final", '2024-08-06T10:00:00+02:00', 'https://peacocktv.smart.link/w7hgjbhvn'),
            $this->chamonixRound($eventInfo, "Men's Speed Qualification", '2024-08-06T13:30:00+02:00', 'https://peacocktv.smart.link/112ddz8sl'),

            // 07/08
            $this->chamonixRound($eventInfo, "Men's Lead Semi-Final", '2024-08-07T10:00:00+02:00', 'https://peacocktv.smart.link/902a0tf6y'),
            $this->chamonixRound($eventInfo, "Women's Speed Final", '2024-08-07T12:35:00+02:00', 'https://peacocktv.smart.link/zgc1owtmz'),

            // 08/08
            $this->chamonixRound($eventInfo, "Women's Lead Semi-Final", '2024-08-08T10:00:00+02:00', 'https://peacocktv.smart.link/cb1l4nxko'),
            $this->chamonixRound($eventInfo, "Men's Speed Final", '2024-08-08T12:35:00+02:00', 'https://peacocktv.smart.link/112ddz8sl'),

            // 09/08
            $this->chamonixRound($eventInfo, "Men's Boulder Final", '2024-08-09T10:15:00+02:00', 'https://peacocktv.smart.link/ku613of3g'),
            $this->chamonixRound($eventInfo, "Men's Lead Final", '2024-08-09T12:35:00+02:00', 'https://peacocktv.smart.link/ku613of3g'),

            // 10/08
            $this->chamonixRound($eventInfo, "Women's Boulder Final", '2024-08-10T10:15:00+02:00', 'https://peacocktv.smart.link/emsr7olbq'),
            $this->chamonixRound($eventInfo, "Women's Lead Final", '2024-08-10T12:35:00+02:00', 'https://peacocktv.smart.link/emsr7olbq'),
        ];
    }

    private function shanghaiRound(IFSCEventInfo $eventInfo, string $title, string $startsAt): IFSCRound
    {
        return $this->round($eventInfo, $title, $startsAt, 'Asia/Shanghai', self::OLYMPIC_QUALIFIERS_SHANGHAI_LIVE_STREAM);
    }

    private function budapestRound(IFSCEventInfo $eventInfo, string $title, string $startsAt): IFSCRound
    {
        return $this->round($eventInfo, $title, $startsAt, 'Europe/Budapest', self::OLYMPIC_QUALIFIERS_BUDAPEST_LIVE_STREAM);
    }

    private function chamonixRound(IFSCEventInfo $eventInfo, string $title, string $startsAt, ?string $streamUrl = null): IFSCRound
    {
        return $this->round($eventInfo, $title, $startsAt, 'Europe/Paris', $streamUrl);
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
