<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class JsonCalendar implements IFSCCalendarGeneratorInterface
{
    private const IFSC_EVENT_INFO_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    /** @param IFSCEvent[] $events */
    public function generateForEvents(array $events): string
    {
        $jsonEvents = ['events' => []];

        foreach ($events as $event) {
            $jsonEvents['events'][] = [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'poster' => $event->poster,
                'stream_url' => $event->streamUrl,
                'event_url' => $this->buildUrl($event),
                'start_time' => $this->formatDate($event->startTime),
            ];
        }

        return json_encode($jsonEvents, flags: JSON_PRETTY_PRINT);
    }

    private function buildUrl(IFSCEvent $event): string
    {
        return sprintf(self::IFSC_EVENT_INFO_URL, $event->id);
    }

    public function formatDate(DateTimeImmutable $date): string
    {
        return DateTime::createFromImmutable($date)->format(DateTimeInterface::RFC3339);
    }
}
