<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use Closure;
use DateInterval;
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
        private string $publishedTtl,
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
        // $events = array_filter($events, $this->excludeQualifications());
        $events = array_map($this->eventConvert(), $events);

        $calendar = new Calendar($events);
        $calendar->setProductIdentifier($this->productIdentifier);
        $calendar->setPublishedTTL(new DateInterval($this->publishedTtl));

        return $calendar;
    }

    public function createEvent(IFSCEvent $event): Event
    {
        return (new Event())
            ->setSummary("IFSC: {$event->name}")
            ->setDescription($this->buildDescription($event))
            ->setUrl(new Uri($event->siteUrl))
            ->setOccurrence($this->buildTimeSpan($event));
    }

    public function eventConvert(): Closure
    {
        return fn (IFSCEvent $event): Event => $this->createEvent($event);
    }

    public function excludeQualifications(): Closure
    {
        return fn (IFSCEvent $event): bool => !$this->isQualification($event) || $this->hasSteamLink($event);
    }

    public function buildTimeSpan(IFSCEvent $event): TimeSpan
    {
        return new TimeSpan(
            new DateTime($event->startTime, applyTimeZone: true),
            new DateTime($event->endTime, applyTimeZone: true),
        );
    }

    public function buildDescription(IFSCEvent $event): string
    {
        return "{$event->description}\n\n{$event->siteUrl}";
    }

    private function isQualification(IFSCEvent $event): bool
    {
        return preg_match('~qualifications?~i', $event->name) === 1;
    }

    private function hasSteamLink(IFSCEvent $event): bool
    {
        return !empty($event->streamUrl);
    }
}
