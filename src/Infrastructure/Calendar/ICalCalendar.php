<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\Calendar;

use DateTimeImmutable;
use Exception;
use Override;
use SportClimbing\IfscCalendar\Domain\Calendar\IFSCCalendarGeneratorInterface;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRound;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStarter;
use SportClimbing\IcsGenerator\CalendarFactory;
use SportClimbing\IcsGenerator\IcsGenerator;

final readonly class ICalCalendar implements IFSCCalendarGeneratorInterface
{
    private IcsGenerator $icsGenerator;

    public function __construct(
        CalendarFactory $calendarFactory,
        string $productIdentifier,
        string $publishedTtl,
        string $calendarName,
    ) {
        $this->icsGenerator = new IcsGenerator(
            calendarFactory: $calendarFactory,
            productIdentifier: $productIdentifier,
            publishedTtl: $publishedTtl,
            calendarName: $calendarName,
        );
    }

    /**
     * @param IFSCEvent[] $events
     * @inheritDoc
     * @throws Exception
     */
    #[Override] public function generateForEvents(array $events): string
    {
        return $this->icsGenerator->generateForEvents(
            array_map($this->convertEventToArray(...), $events)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function convertEventToArray(IFSCEvent $event): array
    {
        return [
            'name' => $event->eventName,
            'location' => $event->location,
            'country' => $event->country,
            'country_name' => $event->countryName,
            'site_url' => $event->siteUrl,
            'starts_at' => $this->formatDateTime($event->startsAt),
            'ends_at' => $this->formatDateTime($event->endsAt),
            'timezone' => $event->timeZone->getName(),
            'league_name' => $event->leagueName,
            'tickets' => [
                'summary' => $event->ticketsSummary ?? '',
                'purchase_url' => $event->ticketsPurchaseUrl ?? '',
            ],
            'rounds' => array_map(fn (IFSCRound $round): array => $this->convertRoundToArray($round), $event->rounds),
            'start_list' => array_map($this->convertStarterToArray(...), $event->startList),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function convertRoundToArray(IFSCRound $round): array
    {
        return [
            'name' => $round->name,
            'categories' => array_map(fn ($c) => $c->value, $round->categories),
            'disciplines' => array_map(fn ($d) => $d->calendarDiscipline(), $round->disciplines->all()),
            'kind' => $round->kind->value,
            'starts_at' => $this->formatDateTime($round->startTime),
            'ends_at' => $this->formatDateTime($round->endTime),
            'schedule_status' => $round->status->value,
            'stream_url' => $round->liveStream->url,
            'stream_blocked_regions' => $round->liveStream->restrictedRegions,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function convertStarterToArray(IFSCStarter $starter): array
    {
        return [
            'first_name' => $starter->firstName,
            'last_name' => $starter->lastName,
            'country' => $starter->country,
            'category' => $starter->gender?->value,
        ];
    }

    private function formatDateTime(DateTimeImmutable $dt): string
    {
        return $dt->format('Y-m-d\TH:i:sP');
    }
}
