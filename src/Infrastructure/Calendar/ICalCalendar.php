<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\Calendar;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Entity\TimeZone;
use Eluceo\iCal\Domain\Enum\EventStatus;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\Alarm\DisplayAction;
use Eluceo\iCal\Domain\ValueObject\Alarm\RelativeTrigger;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Exception;
use SportClimbing\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRound;
use Override;

final readonly class ICalCalendar implements IFSCCalendarGeneratorInterface
{
    private const string DISCORD_URL = 'https://discord.gg/rbM5vjcVHM';
    private const array SITE_URL_PARAMS = [
        'utm_source' => 'calendar',
    ];

    public function __construct(
        private CalendarFactory $calendarFactory,
        private string $productIdentifier,
        private string $publishedTtl,
        private string $calendarName,
    ) {
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override] public function generateForEvents(array $events): string
    {
        return (string) $this->calendarFactory->createNamedCalendar(
            $this->createCalenderFromEvents($events),
            $this->calendarName,
        );
    }

    /**
     * @param IFSCEvent[] $events
     * @throws Exception
     */
    private function createCalenderFromEvents(array $events): Calendar
    {
        $calendar = new Calendar($this->createEvents($events));
        $calendar->setProductIdentifier($this->productIdentifier);
        $calendar->setPublishedTTL(new DateInterval($this->publishedTtl));

        $begin = new DateTimeImmutable('first day of January this year');
        $end = new DateTimeImmutable('last day of December next year');

        foreach ($this->collectTimeZones($events) as $timeZone) {
            $calendar->addTimeZone(TimeZone::createFromPhpDateTimeZone($timeZone, $begin, $end));
        }

        return $calendar;
    }

    /**
     * @param IFSCEvent[] $events
     * @return array<string, DateTimeZone>
     */
    private function collectTimeZones(array $events): array
    {
        $timeZones = [];

        foreach ($events as $event) {
            foreach ($event->rounds as $round) {
                $tz = $round->startTime->getTimezone();
                $timeZones[$tz->getName()] = $tz;
            }
            $tz = $event->startsAt->getTimezone();
            $timeZones[$tz->getName()] = $tz;
        }

        return $timeZones;
    }

    /**
     * @param IFSCEvent[] $events
     * @return Event[]
     * @throws Exception
     */
    private function createEvents(array $events): array
    {
        $calendarEvents = [];

        foreach ($events as $event) {
            if ($this->shouldIgnoreEvent($event)) {
                continue;
            }

            $rounds = $this->getStreamableRounds($event);

            if (!empty($rounds)) {
                foreach ($rounds as $round) {
                    $calendarEvents[] = $this->createEvent($event, $round);
                }
            } else {
                $calendarEvents[] = $this->createEventWithoutRounds($event);
            }
        }

        return $calendarEvents;
    }

    private function createEvent(IFSCEvent $event, IFSCRound $round): Event
    {
        $calendarEvent = new Event()
            ->setSummary(sprintf("%s - %s (%s)", $round->name, $event->location, $event->country))
            ->setDescription($this->buildDescription($event, $round))
            ->setUrl(new Uri($this->buildSiteUrl($event)))
            ->setStatus($this->getEventStatus($round))
            ->setLocation(new Location($this->buildLocation($event)))
            ->setOccurrence($this->buildTimeSpan($round));

        if ($round->status->isConfirmed()) {
            $calendarEvent->addAlarm(
                $this->createAlarmOneHourBefore($event, $round->name),
            );
        }

        return $calendarEvent;
    }

    /** @throws Exception */
    private function createEventWithoutRounds(IFSCEvent $event): Event
    {
        $calendarEvent = new Event()
            ->setSummary(sprintf('%s (%s)', $event->eventName, $event->country))
            ->setDescription($this->buildDescription($event))
            ->setUrl(new Uri($this->buildSiteUrl($event)))
            ->setStatus(EventStatus::TENTATIVE())
            ->setLocation(new Location($this->buildLocation($event)))
            ->setOccurrence($this->buildGenericTimeSpan($event));

        $calendarEvent->addAlarm(
            $this->createAlarmOneDayBefore($event, $event->eventName),
        );

        return $calendarEvent;
    }

    private function buildTimeSpan(IFSCRound $round): TimeSpan
    {
        return new TimeSpan(
            new DateTime($round->startTime, applyTimeZone: true),
            new DateTime($round->endTime, applyTimeZone: true),
        );
    }

    private function buildGenericTimeSpan(IFSCEvent $event): TimeSpan
    {
        return new TimeSpan(
            new DateTime($event->startsAt, applyTimeZone: true),
            new DateTime($event->endsAt, applyTimeZone: true),
        );
    }

    private function buildDescription(IFSCEvent $event, ?IFSCRound $round = null): string
    {
        $description = "{$event->eventName}\n\n";

        if ($round?->status->isProvisional()) {
            $description .= "⚠️ Schedule is provisional and might change. ";
            $description .= "This calendar will update automatically once it's confirmed!\n\n";
        } elseif ($round === null) {
            $description .= "⚠️ Precise schedule has not been announced yet. ";
            $description .= "This calendar will update automatically once it's published!\n\n";
        }

        $description .= "🍿 Stream URL:\n{$this->buildSiteUrl($event)}\n\n";

        if ($event->ticketsPurchaseUrl) {
            $description .= "🎟️ Buy Tickets:\n{$event->ticketsPurchaseUrl}\n\n";
        }

        $description .= "💬 Join Discord:\n" . self::DISCORD_URL . "\n\n";

        $description .= "☕️ If you find this useful, please consider buying me a coffee:\n";
        $description .= "https://www.buymeacoffee.com/sportclimbing\n\n";

        $description .= "🐛 Report a bug/problem:\n";
        $description .= "https://github.com/sportclimbing/ifsc-calendar/issues\n";

        if (array_slice($event->startList, 0, 20)) {
            $description .= "\n📋 Start List:\n";

            foreach ($event->startList as $athlete) {
                $description .= " - {$athlete->firstName} {$athlete->lastName} ({$athlete->country})\n";
            }

            $description .= " - ...\n";
        }

        return $description;
    }

    private function buildLocation(IFSCEvent $event): string
    {
        if ($event->countryName !== '') {
            return "{$event->location}, {$event->countryName}";
        }

        return "{$event->location} ({$event->country})";
    }

    private function buildSiteUrl(IFSCEvent $event): string
    {
        $separator = str_contains($event->siteUrl, '?') ? '&' : '?';
        $params = http_build_query(self::SITE_URL_PARAMS);

        return "{$event->siteUrl}{$separator}{$params}";
    }

    private function getEventStatus(IFSCRound $round): EventStatus
    {
        return $round->status->isConfirmed()
            ? EventStatus::CONFIRMED()
            : EventStatus::TENTATIVE();
    }

    /** @return IFSCRound[] */
    private function getStreamableRounds(IFSCEvent $event): array
    {
        return array_filter($event->rounds, fn (IFSCRound $round): bool => $this->roundIsStreamable($round));
    }

    private function roundIsStreamable(IFSCRound $round): bool
    {
        return !$round->kind->isQualification() || $round->liveStream->hasUrl();
    }

    private function createAlarmOneHourBefore(IFSCEvent $event, string $name): Alarm
    {
        return $this->createAlarm($event, $name, timeBefore: '1 hour');
    }

    private function createAlarmOneDayBefore(IFSCEvent $event, string $name): Alarm
    {
        return $this->createAlarm($event, $name, timeBefore: '1 day');
    }

    private function createAlarm(IFSCEvent $event, string $name, string $timeBefore): Alarm
    {
        $trigger = new RelativeTrigger(
            DateInterval::createFromDateString(datetime: "-{$timeBefore}"),
        );

        return new Alarm(
            new DisplayAction(
                description: "Reminder: {$name} - {$event->location} ({$event->country}) starts in {$timeBefore}!"
            ),
            $trigger->withRelationToEnd(),
        );
    }

    private function shouldIgnoreEvent(IFSCEvent $event): bool
    {
        return $event->leagueName === 'Games';
    }
}
