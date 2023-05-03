<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use Closure;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class ICalCalendar implements IFSCCalendarGeneratorInterface
{
    public function __construct(
        private CalendarFactory $calendarFactory,
        private string $productIdentifier,
        private string $eventBaseUrl,
    ) {
    }

    /** @param IFSCEvent[] $events */
    public function generateForEvents(array $events): string
    {
        return (string) $this->calendarFactory->createCalendar(
            $this->createCalenderFromEvents($events)
        );
    }

    public function createCalenderFromEvents(array $events): Calendar
    {
        $calendarEvents = array_map(
            $this->eventConvert(),
            $events
        );

        $calendar = new Calendar($calendarEvents);
        $calendar->setProductIdentifier($this->productIdentifier);

        return $calendar;
    }

    public function createEvent(IFSCEvent $event): Event
    {
        return (new Event())
            ->setSummary("IFSC: {$event->name}")
            ->setDescription($event->description)
            ->setUrl($this->buildUrl($event))
            ->setOccurrence($this->buildTimeSpan($event));
    }

    public function eventConvert(): Closure
    {
        return fn (IFSCEvent $event): Event => $this->createEvent($event);
    }

    public function buildUrl(IFSCEvent $event): Uri
    {
        return new Uri(sprintf('%s#event-%d', $this->eventBaseUrl, $event->id));
    }

    public function buildTimeSpan(IFSCEvent $event): TimeSpan
    {
        return new TimeSpan(
            new DateTime($event->startTime, applyTimeZone: true),
            new DateTime($event->endTime, applyTimeZone: true),
        );
    }
}
