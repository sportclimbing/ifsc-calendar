<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use DateTime;
use DateTimeInterface;
use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundCategory;
use nicoSWD\IfscCalendar\Domain\StartList\IFSCStarter;
use Override;

final readonly class JsonCalendar implements IFSCCalendarGeneratorInterface
{
    private const string WORLD_CLIMBING_INFO_URL = 'https://www.worldclimbing.com/events/%s';

    private const string GENERATED_BY_URL = 'https://github.com/sportclimbing/ifsc-calendar';

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override] public function generateForEvents(array $events): string
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
                'league_name' => $event->leagueName,
                'season' => $event->season->value,
                'name' => $event->eventName,
                'country' => $event->country,
                'location' => $event->location,
                'poster' => $event->poster,
                'site_url' => $event->siteUrl,
                'event_url' => $this->buildUrl($event),
                'disciplines' => $event->disciplines,
                'starts_at' => $this->formatDate($event->startsAt),
                'ends_at' => $this->formatDate($event->endsAt),
                'timezone' => $event->timeZone->getName(),
                'rounds' => $this->formatRound($event->rounds),
                'start_list' => $this->formatStarters($event->startList),
            ];
        }

        return json_encode($jsonEvents, flags: JSON_PRETTY_PRINT);
    }

    /**
     * @param IFSCRound[] $rounds
     * @return array<mixed>
     */
    private function formatRound(array $rounds): array
    {
        $format = fn (IFSCRound $round): array => [
            'name' => $round->name,
            'categories' => $this->buildCategories($round),
            'disciplines' => $this->buildDisciplines($round),
            'kind' => $round->kind->value,
            'starts_at' => $this->formatDate($round->startTime),
            'ends_at' => $this->formatDate($round->endTime),
            'schedule_status' => $round->status->value,
            'stream_url' => $round->liveStream->url,
            'stream_blocked_regions' => $round->liveStream->restrictedRegions,
        ];

        return array_map($format, $rounds);
    }

    /**
     * @param IFSCStarter[] $starters
     * @return array<array<string, string|null>>
     */
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
        return sprintf(self::WORLD_CLIMBING_INFO_URL, $event->slug);
    }

    /** @return string[] */
    private function buildDisciplines(IFSCRound $round): array
    {
        return array_map(static fn (IFSCDiscipline $discipline): string => $discipline->value, $round->disciplines->all());
    }

    /** @return string[] */
    private function buildCategories(IFSCRound $round): array
    {
        return array_map(static fn (IFSCRoundCategory $category): string => $category->value, $round->categories);
    }

    private function formatDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(DateTimeInterface::RFC3339);
    }
}
