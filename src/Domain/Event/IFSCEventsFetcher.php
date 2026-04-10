<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JsonException;
use SportClimbing\IfscCalendar\Domain\DomainEvent\Event\EventScrapingStartedEvent;
use SportClimbing\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventCategory;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventRound;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRound;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundFactory;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundKind;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundStatus;
use SportClimbing\IfscCalendar\Domain\Schedule\IFSCSchedule;
use SportClimbing\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;
use Override;
use RuntimeException;
use Throwable;

final readonly class IFSCEventsFetcher implements IFSCEventFetcherInterface
{
    public function __construct(
        private IFSCEventFactory $eventFactory,
        private IFSCRoundFactory $roundFactory,
        private IFSCScheduleFactory $scheduleFactory,
        private EventDispatcherInterface $eventDispatcher,
        private IFSCEventSlug $eventSlug,
    ) {
    }

    /** @inheritdoc */
    #[Override] public function fetchEventsForSeason(
        IFSCSeasonYear $season,
        array $selectedLeagues,
        string $schedulePath,
    ): array {
        $events = [];

        foreach ($this->loadEventsFromFile($schedulePath) as $eventData) {
            if (!$this->isFromSeason($eventData, $season)) {
                continue;
            }

            if (!$this->isFromSelectedLeague($eventData, $selectedLeagues)) {
                continue;
            }

            $this->emitEventGenerationStarted($eventData);

            $event = $this->hydrateEventInfo($eventData);
            $parsedRounds = $this->hydrateSchedules($eventData, $event->timeZone);

            if (!empty($parsedRounds)) {
                $rounds = $this->hydrateRounds(
                    event: $event,
                    parsedRounds: $parsedRounds,
                );
            } else {
                $rounds = $this->generateRounds($event);
            }

            $events[] = $this->eventFactory->create(
                season: $season,
                event: $event,
                rounds: $rounds,
            );
        }

        return $events;
    }

    /** @param array<string,mixed> $eventData */
    private function emitEventGenerationStarted(array $eventData): void
    {
        $this->eventDispatcher->dispatch(
            new EventScrapingStartedEvent(
                eventName: $this->requiredString($eventData, 'event_name'),
            ),
        );
    }

    /** @return array<int,array<string,mixed>> */
    private function loadEventsFromFile(string $schedulePath): array
    {
        if (!is_file($schedulePath)) {
            throw new RuntimeException("Schedule file not found: {$schedulePath}");
        }

        if (!is_readable($schedulePath)) {
            throw new RuntimeException("Schedule file is not readable: {$schedulePath}");
        }

        $contents = file_get_contents($schedulePath);

        if ($contents === false) {
            throw new RuntimeException("Unable to read schedule file: {$schedulePath}");
        }

        try {
            $decoded = json_decode($contents, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException("Invalid JSON in schedule file '{$schedulePath}': {$e->getMessage()}");
        }

        $events = $decoded['events'] ?? null;

        if (!is_array($events)) {
            throw new RuntimeException("Invalid schedule payload in '{$schedulePath}': missing 'events' array");
        }

        return $events;
    }

    /** @param array<string,mixed> $eventData */
    private function isFromSeason(array $eventData, IFSCSeasonYear $season): bool
    {
        $date = $this->requiredString($eventData, 'local_start_date');

        return (int) substr($date, 0, 4) === $season->value;
    }

    /**
     * @param array<string,mixed> $eventData
     * @param string[] $selectedLeagues
     */
    private function isFromSelectedLeague(array $eventData, array $selectedLeagues): bool
    {
        return in_array($this->requiredString($eventData, 'league_name'), $selectedLeagues, true);
    }

    /** @param array<string,mixed> $eventData */
    private function hydrateEventInfo(array $eventData): IFSCEventInfo
    {
        $timeZone = $this->createTimeZone(
            $this->requiredString($eventData, 'timezone'),
            $eventData,
        );
        $disciplines = $this->parseDisciplines($eventData);
        $eventName = $this->requiredString($eventData, 'event_name');

        return new IFSCEventInfo(
            eventId: $this->requiredInt($eventData, 'event_id'),
            eventName: $eventName,
            slug: $this->eventSlug->create($eventName),
            leagueId: $this->optionalInt($eventData, 'league_id'),
            leagueName: $this->requiredString($eventData, 'league_name'),
            leagueSeasonId: $this->optionalInt($eventData, 'league_season_id'),
            localStartDate: $this->requiredString($eventData, 'local_start_date'),
            localEndDate: $this->requiredString($eventData, 'local_end_date'),
            timeZone: $timeZone,
            location: $this->parseLocationName($eventData),
            country: $this->parseCountryCode($eventData),
            disciplines: $disciplines,
            categories: $this->parseCategories($eventData, $disciplines),
            infosheetUrl: $this->optionalString($eventData, 'infosheet_url'),
            ticketsSummary: $this->optionalString($eventData['tickets'] ?? [], 'summary'),
            ticketsPurchaseUrl: $this->optionalString($eventData['tickets'] ?? [], 'purchase_url'),
        );
    }

    /**
     * @param IFSCEventInfo $event
     * @param IFSCSchedule[] $parsedRounds
     * @return array @IFSCRound[]
     */
    private function hydrateRounds(IFSCEventInfo $event, array $parsedRounds): array
    {
        $rounds = [];

        foreach ($parsedRounds as $round) {
            $rounds[] = $this->roundFactory->create(
                event: $event,
                roundName: $round->name,
                startTime: $round->startsAt,
                endTime: $round->endsAt,
                status: IFSCRoundStatus::PROVISIONAL,
            );
        }

        return $rounds;
    }

    /** @param array<string,mixed> $eventData */
    private function parseLocationName(array $eventData): string
    {
        $location = $eventData['location'] ?? null;

        if (is_array($location)) {
            return $this->requiredString($location, 'name');
        }

        return $this->requiredString($eventData, 'location');
    }

    /** @param array<string,mixed> $eventData */
    private function parseCountryCode(array $eventData): string
    {
        $location = $eventData['location'] ?? null;

        if (is_array($location)) {
            $countryCode = $location['country_code'] ?? null;
            if (is_string($countryCode) && trim($countryCode) !== '') {
                return $countryCode;
            }

            $country = $location['country'] ?? null;
            if (is_string($country) && trim($country) !== '') {
                return $country;
            }
        }

        return $this->requiredString($eventData, 'country');
    }

    /**
     * @param array<string,mixed> $eventData
     * @return IFSCDiscipline[]
     */
    private function parseDisciplines(array $eventData): array
    {
        $disciplines = $eventData['disciplines'] ?? null;
        if (!is_array($disciplines)) {
            throw new RuntimeException(sprintf("Invalid disciplines for %s", $this->eventReference($eventData)));
        }

        $parsed = [];

        foreach ($disciplines as $discipline) {
            if (!is_string($discipline)) {
                throw new RuntimeException(sprintf("Invalid discipline value for %s", $this->eventReference($eventData)));
            }

            $parsed[$discipline] = IFSCDiscipline::from($discipline);
        }

        return array_values($parsed);
    }

    /**
     * @param array<string,mixed> $eventData
     * @param IFSCDiscipline[] $disciplines
     * @return IFSCEventCategory[]
     */
    private function parseCategories(array $eventData, array $disciplines): array
    {
        $categoriesPayload = $eventData['categories'] ?? $eventData['d_cats'] ?? [];
        if (!is_array($categoriesPayload)) {
            throw new RuntimeException(sprintf("Invalid categories for %s", $this->eventReference($eventData)));
        }

        $categories = [];

        foreach ($categoriesPayload as $categoryPayload) {
            if (is_string($categoryPayload)) {
                $rounds = $this->buildEstimatedCategoryRounds($categoryPayload, $disciplines);
                if (!empty($rounds)) {
                    $categories[] = new IFSCEventCategory($rounds);
                }

                continue;
            }

            if (!is_array($categoryPayload)) {
                throw new RuntimeException(sprintf("Invalid category payload for %s", $this->eventReference($eventData)));
            }

            $roundsPayload = $categoryPayload['rounds'] ?? $categoryPayload['category_rounds'] ?? [];
            if (!is_array($roundsPayload)) {
                throw new RuntimeException(sprintf("Invalid category rounds for %s", $this->eventReference($eventData)));
            }

            $rounds = [];
            foreach ($roundsPayload as $roundPayload) {
                if (!is_array($roundPayload)) {
                    throw new RuntimeException(sprintf("Invalid round payload for %s", $this->eventReference($eventData)));
                }

                $rounds[] = $this->parseEventRound($roundPayload, $eventData);
            }

            if (empty($rounds) && isset($categoryPayload['name']) && is_string($categoryPayload['name'])) {
                $rounds = $this->buildEstimatedCategoryRounds($categoryPayload['name'], $disciplines);
            }

            if (!empty($rounds)) {
                $categories[] = new IFSCEventCategory($rounds);
            }
        }

        return $categories;
    }

    /**
     * @param IFSCDiscipline[] $disciplines
     * @return IFSCEventRound[]
     */
    private function buildEstimatedCategoryRounds(string $category, array $disciplines): array
    {
        $category = strtolower(trim($category));

        if ($category === '') {
            return [];
        }

        $rounds = [];
        foreach ($disciplines as $discipline) {
            foreach ([IFSCRoundKind::QUALIFICATION, IFSCRoundKind::SEMI_FINAL, IFSCRoundKind::FINAL] as $kind) {
                $rounds[] = new IFSCEventRound(
                    discipline: $discipline->value,
                    kind: $kind,
                    category: $category,
                );
            }
        }

        return $rounds;
    }

    /**
     * @param array<string,mixed> $roundPayload
     * @param array<string,mixed> $eventData
     */
    private function parseEventRound(array $roundPayload, array $eventData): IFSCEventRound
    {
        $disciplineKey = isset($roundPayload['discipline']) ? 'discipline' : 'kind';
        $kindKey = isset($roundPayload['discipline']) ? 'kind' : 'name';

        $discipline = $this->requiredString($roundPayload, $disciplineKey);
        $kind = $this->parseRoundKind(
            roundKind: $this->requiredString($roundPayload, $kindKey),
            eventData: $eventData,
        );

        return new IFSCEventRound(
            discipline: $discipline,
            kind: $kind,
            category: $this->requiredString($roundPayload, 'category'),
        );
    }

    /** @param array<string,mixed> $eventData */
    private function parseRoundKind(string $roundKind, array $eventData): IFSCRoundKind
    {
        $normalizedRoundKind = strtolower(
            str_replace(' ', '-', $roundKind),
        );

        try {
            return IFSCRoundKind::from($normalizedRoundKind);
        } catch (Throwable) {
            throw new RuntimeException(
                sprintf("Invalid round kind '%s' for %s", $roundKind, $this->eventReference($eventData)),
            );
        }
    }

    /**
     * @return IFSCRound[]
     * @throws Exception
     */
    private function generateRounds(IFSCEventInfo $event): array
    {
        $rounds = [];

        foreach ($event->categories as $category) {
            foreach ($category->rounds as $round) {
                $startTime = $this->estimatedLocalStartTime($event);

                $rounds[] = $this->roundFactory->create(
                    event: $event,
                    roundName: $this->normalizeRoundName($round),
                    startTime: $startTime,
                    endTime: $startTime->modify('+90 minutes'),
                    status: IFSCRoundStatus::ESTIMATED,
                );
            }
        }

        return $rounds;
    }

    private function normalizeRoundName(IFSCEventRound $round): string
    {
        $discipline = preg_replace_callback(
            pattern: '~(\w)&(\w)~',
            callback: static fn (array $match): string => $match[1] . ' & ' . $match[2],
            subject: $round->discipline,
        );

        return sprintf("%s's %s %s", $round->category, $discipline, $round->kind->value) |> ucwords(...);
    }

    private function estimatedLocalStartTime(IFSCEventInfo $event): DateTimeImmutable
    {
        return $this->createLocalDate("{$event->localStartDate} 08:00", $event->timeZone);
    }

    private function createLocalDate(string $date, DateTimeZone $timeZone): DateTimeImmutable
    {
        return new DateTimeImmutable($date)->setTimezone($timeZone);
    }

    /**
     * @param array<string,mixed> $eventData
     * @param DateTimeZone $eventTimeZone
     * @return IFSCSchedule[]
     */
    private function hydrateSchedules(array $eventData, DateTimeZone $eventTimeZone): array
    {
        $schedule = $eventData['schedule'] ?? [];
        if (!is_array($schedule) || empty($schedule)) {
            return [];
        }

        $parsed = [];

        foreach ($schedule as $rawSchedule) {
            if (!is_array($rawSchedule)) {
                throw new RuntimeException(sprintf("Invalid schedule entry for %s", $this->eventReference($eventData)));
            }

            $round = $this->scheduleFactory->create(
                name: $this->requiredString($rawSchedule, 'name'),
                startsAt: $this->createDateTime(
                    value: $this->requiredString($rawSchedule, 'starts_at'),
                    timeZone: $eventTimeZone,
                    eventData: $eventData,
                ),
                endsAt: $this->parseOptionalDateTime(
                    schedule: $rawSchedule,
                    key: 'ends_at',
                    timeZone: $eventTimeZone,
                    eventData: $eventData,
                ),
            );

            $parsed[] = $round;
        }

        return $parsed;
    }

    /**
     * @param array<string,mixed> $schedule
     * @param array<string,mixed> $eventData
     */
    private function parseOptionalDateTime(
        array $schedule,
        string $key,
        DateTimeZone $timeZone,
        array $eventData,
    ): ?DateTimeImmutable {
        $value = $schedule[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException(sprintf("Invalid '%s' value for %s", $key, $this->eventReference($eventData)));
        }

        return $this->createDateTime($value, $timeZone, $eventData);
    }

    /** @param array<string,mixed> $eventData */
    private function createDateTime(string $value, DateTimeZone $timeZone, array $eventData): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($value, $timeZone);
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf("Invalid datetime '%s' for %s", $value, $this->eventReference($eventData)),
                previous: $e,
            );
        }
    }

    /** @param array<string,mixed> $eventData */
    private function createTimeZone(string $timeZone, array $eventData): DateTimeZone
    {
        try {
            return new DateTimeZone($timeZone);
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf("Invalid timezone '%s' for %s", $timeZone, $this->eventReference($eventData)),
                previous: $e,
            );
        }
    }

    /**
     * @param array<string,mixed> $payload
     * @throws RuntimeException
     */
    private function optionalString(array $payload, string $key): ?string
    {
        $value = $payload[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new RuntimeException("Invalid '{$key}' in schedule payload");
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * @param array<string,mixed> $payload
     * @throws RuntimeException
     */
    private function requiredString(array $payload, string $key): string
    {
        $value = $payload[$key] ?? null;

        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException("Missing or invalid '{$key}' in schedule payload");
        }

        return $value;
    }

    /**
     * @param array<string,mixed> $payload
     * @throws RuntimeException
     */
    private function requiredInt(array $payload, string $key): int
    {
        $value = $payload[$key] ?? null;

        if (!is_int($value) && !is_numeric($value)) {
            throw new RuntimeException("Missing or invalid '{$key}' in schedule payload");
        }

        return (int) $value;
    }

    /**
     * @param array<string,mixed> $payload
     * @throws RuntimeException
     */
    private function optionalInt(array $payload, string $key, int $default = 0): int
    {
        $value = $payload[$key] ?? null;
        if ($value === null) {
            return $default;
        }

        if (!is_int($value) && !is_numeric($value)) {
            throw new RuntimeException("Invalid '{$key}' in schedule payload");
        }

        return (int) $value;
    }

    /** @param array<string,mixed> $eventData */
    private function eventReference(array $eventData): string
    {
        return sprintf("event '%s'", ($eventData['event_id'] ?? 'unknown'));
    }
}
