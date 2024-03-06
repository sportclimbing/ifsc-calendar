<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Starter\IFSCStarter;
use Override;

final readonly class JsonCalendar implements IFSCCalendarGeneratorInterface
{
    private const string IFSC_EVENT_INFO_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    private const string GENERATED_BY_URL = 'https://github.com/sportclimbing/ifsc-calendar';

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override]
    public function generateForEvents(array $events): string
    {
        $jsonEvents = [
            'events' => [],
            'metadata' => [
                'updated_at' => $this->formatDate(new DateTime()),
                'generated_by' => self::GENERATED_BY_URL,
            ],
        ];

        foreach ($events as $event) {
            $jsonEvents['events'][] = [
                'id' => $event->eventId,
                'season' => $event->season->value,
                'name' => $event->eventName,
                'country' => $event->country,
                'location' => $event->location,
                'poster' => $event->poster,
                'site_url' => $event->siteUrl,
                'event_url' => $this->buildUrl($event),
                'disciplines' => $event->disciplines,
                'starts_at' => $this->formatDateString($event->startsAt, $event->timeZone),
                'ends_at' => $this->formatDateString($event->endsAt, $event->timeZone),
                'timezone' => $event->timeZone,
                'rounds' => $this->formatRound($event->rounds),
                'start_list' => $this->formatStarters($event->starters),
            ];
        }

        return json_encode($jsonEvents, flags: JSON_PRETTY_PRINT);
    }

    /**
     * @param IFSCRound[] $rounds
     * @return IFSCRound[]
     */
    private function formatRound(array $rounds): array
    {
        $format = fn (IFSCRound $round): array => [
            'name' => $round->name,
            'category' => $round->category,
            'kind' => $round->kind?->value,
            'disciplines' => $this->buildDisciplines($round),
            'stream_url' => $round->streamUrl->url,
            'starts_at' => $this->formatDate($round->startTime),
            'ends_at' => $this->formatDate($round->endTime),
            'schedule_confirmed' => $round->scheduleConfirmed,
        ];

        return array_map($format, $rounds);
    }

    private function formatStarters(array $starters): array
    {
        $format = static fn (IFSCStarter $starter): array => [
            'first_name' => $starter->firstName,
            'last_name' => $starter->lastName,
            'country' => $starter->country,
            'photo_url' => $starter->photoUrl,
        ];

        return array_map($format, $starters);
    }

    private function buildUrl(IFSCEvent $event): string
    {
        return sprintf(self::IFSC_EVENT_INFO_URL, $event->eventId);
    }

    private function buildDisciplines(IFSCRound $round): array
    {
        return array_map(static fn (IFSCDiscipline $discipline): string => $discipline->value, $round->disciplines);
    }

    /** @throws Exception */
    private function formatDateString(string $date, string $timeZone): string
    {
        $dateTime = new DateTime($date);
        $dateTime->setTimezone(new DateTimeZone($timeZone));

        return $this->formatDate($dateTime);
    }

    private function formatDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(DateTimeInterface::RFC3339);
    }
}
