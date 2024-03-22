<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
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
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidLeagueName;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use Override;

final readonly class ICalCalendar implements IFSCCalendarGeneratorInterface
{
    public function __construct(
        private CalendarFactory $calendarFactory,
        private string $productIdentifier,
        private string $publishedTtl,
    ) {
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override]
    public function generateForEvents(array $events): string
    {
        return (string) $this->calendarFactory->createCalendar(
            $this->createCalenderFromEvents($events),
        );
    }

    /** @throws Exception */
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
            $rounds = $this->getNonQualificationRounds($event);

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
        $calendarEvent = (new Event())
            ->setSummary(sprintf("IFSC: %s - %s (%s)", $round->name, $event->eventName, $event->country))
            ->setDescription($this->buildDescription($event, $round))
            ->setUrl(new Uri($event->siteUrl))
            ->setStatus($this->getEventStatus($round))
            ->setLocation(new Location("{$event->location} ({$event->country})"))
            ->setOccurrence($this->buildTimeSpan($round));

        if ($round->status->isConfirmed()) {
            $alarm = $this->createAlarmOneHourBefore($event, $round);
        } else {
            $alarm = $this->createAlarmOneDayBefore($event, $round);
        }

        $calendarEvent->addAlarm($alarm);

        return $calendarEvent;
    }

    /** @throws Exception */
    private function createEventWithoutRounds(IFSCEvent $event): Event
    {
        return (new Event())
            ->setSummary(sprintf('%s (%s)', $event->normalizedName(), $event->country))
            ->setDescription($this->buildDescription($event))
            ->setUrl(new Uri($event->siteUrl))
            ->setStatus(EventStatus::TENTATIVE())
            ->setLocation(new Location("{$event->location} ({$event->country})"))
            ->setOccurrence($this->buildGenericTimeSpan($event));
    }

    private function buildTimeSpan(IFSCRound $round): TimeSpan
    {
        return new TimeSpan(
            new DateTime($round->startTime, applyTimeZone: true),
            new DateTime($round->endTime, applyTimeZone: true),
        );
    }

    /** @throws Exception */
    private function buildGenericTimeSpan(IFSCEvent $event): TimeSpan
    {
        $timezone = new DateTimeZone($event->timeZone);

        return new TimeSpan(
            new DateTime(new DateTimeImmutable($event->startsAt, $timezone), applyTimeZone: true),
            new DateTime(new DateTimeImmutable($event->endsAt, $timezone), applyTimeZone: true),
        );
    }

    /** @throws InvalidLeagueName */
    private function buildDescription(IFSCEvent $event, ?IFSCRound $round = null): string
    {
        $description  = "{$event->normalizedName()} ({$event->country})\n\n";

        if (!$round?->status->isConfirmed()) {
            $description .= "⚠️ Precise schedule has not been announced yet. ";
            $description .= "This calendar will update automatically once it's published!\n\n";
        }

        $description.= "League: {$event->leagueName}\n\n";

        $description .= "Disciplines:\n";

        if ($round) {
            foreach ($round->disciplines as $discipline) {
                $description .= " - " . ucfirst($discipline->value) ."\n";
            }
        } else {
            foreach ($event->disciplines as $discipline) {
                $description .= " - " . ucfirst($discipline->value) ."\n";
            }
        }

        $description .= "\n";
        $description.= "Stream URL:\n{$event->siteUrl}\n";

        if ($event->starters) {
            $description .= "\nStart List:\n";

            foreach ($event->starters as $starter) {
                $description .= " - {$starter->firstName} {$starter->lastName} ({$starter->country})\n";
            }

            $description .= " - ...\n";
        }

        return $description;
    }

    private function getEventStatus(IFSCRound $round): EventStatus
    {
        return $round->status->isConfirmed()
            ? EventStatus::CONFIRMED()
            : EventStatus::TENTATIVE();
    }

    /** @return IFSCRound[] */
    private function getNonQualificationRounds(IFSCEvent $event): array
    {
        return array_filter($event->rounds, static fn (IFSCRound $round): bool => !$round->kind->isQualification());
    }

    private function createAlarmOneHourBefore(IFSCEvent $event, IFSCRound $round): Alarm
    {
        return $this->createAlarm($event, $round, timeBefore: '1 hour');
    }

    private function createAlarmOneDayBefore(IFSCEvent $event, IFSCRound $round): Alarm
    {
        return $this->createAlarm($event, $round, timeBefore: '1 day');
    }

    private function createAlarm(IFSCEvent $event, IFSCRound $round, string $timeBefore): Alarm
    {
        $trigger = new RelativeTrigger(
            DateInterval::createFromDateString(datetime: "-{$timeBefore}"),
        );

        return new Alarm(
            new DisplayAction(
                description: "Reminder: IFSC: {$round->name} - {$event->location} ({$event->country}) starts in {$timeBefore}!"
            ),
            $trigger->withRelationToEnd(),
        );
    }
}
