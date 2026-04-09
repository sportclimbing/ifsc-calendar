<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\Calendar;

use DateTime;
use DateTimeInterface;
use Exception;
use SportClimbing\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRound;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundCategory;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStarter;
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
                'slug' => $event->slug,
                'country' => $event->country,
                'country_name' => $this->countryName($event->country),
                'location' => $event->location,
                'site_url' => $event->siteUrl,
                'infosheet_url' => $event->infosheetUrl,
                'event_url' => $this->buildUrl($event),
                'disciplines' => $event->disciplines,
                'starts_at' => $this->formatDate($event->startsAt),
                'ends_at' => $this->formatDate($event->endsAt),
                'timezone' => $event->timeZone->getName(),
                'rounds' => $this->formatRound($event->rounds),
                'start_list' => $this->formatStarters($event->startList),
                'start_list_total' => $event->startListTotal,
            ];
        }

        return json_encode($jsonEvents, flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param IFSCRound[] $rounds
     * @return array
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
        $format = fn (IFSCStarter $starter): array => [
            'athlete_id' => $starter->athleteId,
            'first_name' => $starter->firstName,
            'last_name' => $starter->lastName,
            'country' => $starter->country,
            'photo_url' => $starter->photoUrl,
            'instagram' => $this->normalizeInstagram($starter->instagram),
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

    private function countryName(string $countryCode): string
    {
        // IOC codes that differ from ISO 3166-1 and aren't resolved by Locale
        $iocToIso = [
            'CHI' => 'CHL',
            'GER' => 'DEU',
            'NED' => 'NLD',
            'PHI' => 'PHL',
            'SLO' => 'SVN',
            'SUI' => 'CHE',
        ];

        $isoCode = $iocToIso[$countryCode] ?? $countryCode;

        return \Locale::getDisplayRegion("und-{$isoCode}", 'en');
    }

    private function normalizeInstagram(?string $instagram): ?string
    {
        if ($instagram === null || $instagram === '') {
            return null;
        }

        if (str_contains($instagram, 'instagram.com/')) {
            preg_match('~instagram\.com/([^/?#]+)~', $instagram, $matches);
            return $matches[1] ?? null;
        }

        return ltrim($instagram, '@');
    }

    private function formatDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(DateTimeInterface::RFC3339);
    }
}
