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
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;

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
    public function generateForEvents(array $events): string
    {
        return (string) $this->calendarFactory->createCalendar(
            $this->createCalenderFromEvents($events)
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

    private function createEvent(IFSCEvent $event, IFSCRound $round): Event
    {
        return (new Event())
            ->setSummary("IFSC: {$round->name}")
            ->setDescription($this->buildDescription($event))
            ->setUrl(new Uri($event->siteUrl))
            ->setStatus($this->getEventStatus($round))
            ->setOccurrence($this->buildTimeSpan($round));
    }

    /** @throws Exception */
    private function createEventWithoutRounds(IFSCEvent $event): Event
    {
        return (new Event())
            ->setSummary($event->eventName)
            ->setDescription($this->buildDescription($event, confirmedSchedule: false))
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
        return new TimeSpan(
            new DateTime(new DateTimeImmutable($event->startsAt, new DateTimeZone($event->timeZone)), applyTimeZone: true),
            new DateTime(new DateTimeImmutable($event->endsAt, new DateTimeZone($event->timeZone)), applyTimeZone: true),
        );
    }

    private function buildDescription(IFSCEvent $event, bool $confirmedSchedule = true): string
    {
        $description  = "{$event->eventName}\n\n";

        if (!$confirmedSchedule) {
            $description .= "⚠️ Precise schedule has not been announced yet. This calendar will update automatically once it's published!\n\n";
        }

        $description.= "Stream URL:\n{$event->siteUrl}\n";

        if ($event->starters) {
            $description .= "\nStart List:\n";
        }

        foreach ($event->starters as $starter) {
            $description .= " - {$starter->firstName} {$starter->lastName} ({$starter->country})\n";
        }

        if ($event->starters) {
            $description .= " - ...\n";
        }

        return $description;
    }

    private function getEventStatus(IFSCRound $round): EventStatus
    {
        return $round->scheduleConfirmed
            ? EventStatus::CONFIRMED()
            : EventStatus::TENTATIVE();
    }

    /** @return IFSCRound[] */
    private function getNonQualificationRounds(IFSCEvent $event): array
    {
        return array_filter($event->rounds, fn (IFSCRound $round): bool => !$this->isQualificationRound($round));
    }

    private function isQualificationRound(IFSCRound $round): bool
    {
        return preg_match('~qualifications?~i', $round->name) === 1;
    }
}
