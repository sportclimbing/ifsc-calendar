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
use nicoSWD\IfscCalendar\Domain\Calendar\CalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class ICalCalendar implements CalendarGeneratorInterface
{
    private const IFSC_EVENT_INFO_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    public function __construct(
        private CalendarFactory $calendarFactory,
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
        return new Calendar(
            array_map(
                $this->eventConvert(),
                $events
            )
        );
    }

    public function createEvent(IFSCEvent $event): Event
    {
        return (new Event())
            ->setSummary($event->name)
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
        return new Uri(sprintf(self::IFSC_EVENT_INFO_URL, $event->id));
    }

    public function buildTimeSpan(IFSCEvent $event): TimeSpan
    {
        return new TimeSpan(
            new DateTime($event->startTime, applyTimeZone: true),
            new DateTime($event->endTime, applyTimeZone: true),
        );
    }
}
