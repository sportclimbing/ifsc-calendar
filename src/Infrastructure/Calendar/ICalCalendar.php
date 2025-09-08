<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use DateInterval;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Enum\EventStatus;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\Alarm\DisplayAction;
use Eluceo\iCal\Domain\ValueObject\Alarm\RelativeTrigger;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidLeagueName;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use Override;

final readonly class ICalCalendar implements IFSCCalendarGeneratorInterface
{
    private const string DISCORD_URL = 'https://discord.gg/rbM5vjcVHM';

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

        return $calendar;
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

    /** @throws InvalidLeagueName */
    private function createEvent(IFSCEvent $event, IFSCRound $round): Event
    {
        $calendarEvent = new Event()
            ->setSummary(sprintf("IFSC: %s - %s (%s)", $round->name, $event->location, $event->country))
            ->setDescription($this->buildDescription($event, $round))
            ->setUrl(new Uri($event->siteUrl))
            ->setStatus($this->getEventStatus($round))
            ->setLocation(new Location("{$event->location} ({$event->country})"))
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
            ->setSummary(sprintf('%s (%s)', $event->normalizedName(), $event->country))
            ->setDescription($this->buildDescription($event))
            ->setUrl(new Uri($event->siteUrl))
            ->setStatus(EventStatus::TENTATIVE())
            ->setLocation(new Location("{$event->location} ({$event->country})"))
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

    /** @throws InvalidLeagueName */
    private function buildDescription(IFSCEvent $event, ?IFSCRound $round = null): string
    {
        $description = "🏆 {$event->normalizedName()} ({$event->country})\n\n";

        if ($round?->status->isProvisional()) {
            $description .= "⚠️ Schedule is provisional and might change. ";
            $description .= "This calendar will update automatically once it's confirmed!\n\n";
        } elseif ($round === null) {
            $description .= "⚠️ Precise schedule has not been announced yet. ";
            $description .= "This calendar will update automatically once it's published!\n\n";
        }

        $description .= "🧗 League:\n{$event->leagueName}\n\n";
        $description .= "🍿 Stream URL:\n{$event->siteUrl}\n\n";
        $description .= "💬 Join Discord:\n" . self::DISCORD_URL . "\n";

        if ($event->startList) {
            $description .= "\n📋 Start List:\n";

            foreach ($event->startList as $athlete) {
                $description .= " - {$athlete->firstName} {$athlete->lastName} ({$athlete->country})\n";
            }

            $description .= " - ...\n";
        }

        $description .= "\n☕️ If you find this useful, please consider buying me a coffee:\n";
        $description .= "https://www.buymeacoffee.com/sportclimbing\n\n";

        $description .= "🐛 Report a bug/problem:\n";
        $description .= "https://github.com/sportclimbing/ifsc-calendar/issues\n";

        return $description;
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
        return array_filter($event->rounds, fn (IFSCRound $round): bool => $round->isStreamable());
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
                description: "Reminder: IFSC: {$name} - {$event->location} ({$event->country}) starts in {$timeBefore}!"
            ),
            $trigger->withRelationToEnd(),
        );
    }
}
