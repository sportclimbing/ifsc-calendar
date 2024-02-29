<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use DateInterval;
use DateTimeImmutable;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
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
            if (!empty($event->rounds)) {
                foreach ($event->rounds as $round) {
                    if (!$this->isQualificationRound($round)) {
                        $calendarEvents[] = $this->createEvent($event, $round);
                    }
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
            ->setOccurrence($this->buildTimeSpan($round));
    }

    /** @throws Exception */
    private function createEventWithoutRounds(IFSCEvent $event): Event
    {
        return (new Event())
            ->setSummary($event->eventName)
            ->setDescription($this->buildDescription($event))
            ->setUrl(new Uri($event->siteUrl))
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
            new DateTime(new DateTimeImmutable($event->startsAt), applyTimeZone: true),
            new DateTime(new DateTimeImmutable($event->endsAt), applyTimeZone: true),
        );
    }

    private function buildDescription(IFSCEvent $event): string
    {
        return "{$event->eventName}\n\n{$event->siteUrl}";
    }

    private function isQualificationRound(IFSCRound $round): bool
    {
        return preg_match('~qualifications?~i', $round->name) === 1;
    }
}
