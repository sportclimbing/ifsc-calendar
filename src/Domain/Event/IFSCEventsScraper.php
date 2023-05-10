<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DOMElement;
use DOMXPath;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

final readonly class IFSCEventsScraper
{
    private const IFSC_EVENT_PAGE_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    public function __construct(
        private HttpClientInterface $client,
        private IFSCEventFactory $eventFactory,
        private DOMHelper $domHelper,
        private Normalizer $normalizer,
    ) {
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws InvalidURLException
     * @return IFSCEvent[]
     */
    public function fetchEventsForLeague(int $season, int $eventId, string $timezone, string $eventName): array
    {
        $xpath = $this->getXPathForEventsWithId($eventId);
        $dateRegex = $this->buildDateRegex();
        /** @var IFSCSchedule[] $schedules */
        $schedules = [];

        foreach ($this->domHelper->getParagraphs($xpath) as $paragraph) {
            if (!preg_match_all($dateRegex, $this->normalize($paragraph), $matches)) {
                continue;
            }

            foreach ($matches['day'] as $key => $match) {
                foreach ($this->normalizer->nonEmptyLines($matches['times'][$key]) as $line) {
                    [$eventTime, $cupName, $streamUrl] = $this->parseEventDetails($line);

                    $schedules[] = IFSCSchedule::create(
                        day: (int) $matches['day'][$key],
                        month: Month::fromName($matches['month'][$key]),
                        time: $eventTime,
                        season: $season,
                        cupName: $cupName,
                        streamUrl: $streamUrl,
                    );
                }
            }
        }

        $poster = $this->domHelper->getPoster($xpath);
        $events = [];

        foreach ($schedules as $schedule) {
            $startDateTime = $this->getStartDateTime($schedule, $timezone);
            $endDateTime = $this->getEndDateTime($startDateTime);

            $events[] = $this->eventFactory->create(
                name: $schedule->cupName,
                id: $eventId,
                description: $eventName,
                streamUrl: $schedule->streamUrl,
                poster: $poster,
                startTime: $startDateTime,
                endTime: $endDateTime,
            );
        }

        return $events;
    }

    private function getXPathForEventsWithId(int $eventId): DOMXPath
    {
        return $this->domHelper->htmlToXPath(
            $this->client->get($this->buildLeagueUri($eventId))
        );
    }

    private function getStartDateTime(IFSCSchedule $schedule, string $timezone): DateTimeImmutable
    {
        [$hour, $minute] = sscanf($schedule->time, '%d:%d');

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone($timezone));
        $date->setDate($schedule->season, $schedule->month->value, $schedule->day);
        $date->setTime($hour, $minute);

        return DateTimeImmutable::createFromMutable($date);
    }

    private function getEndDateTime(DateTimeImmutable $date): DateTimeImmutable
    {
        $endDate = DateTime::createFromImmutable($date);
        $endDate->modify('+3 hours');

        return DateTimeImmutable::createFromMutable($endDate);
    }

    private function buildDateRegex(): string
    {
        $months = implode('|', Month::monthNames());

        return "~
            (?:MONDAY|TUESDAY|WEDNESDAY|THURSDAY|FRIDAY|SATURDAY|SUNDAY),\s+
            (?<day>\d{1,2})\s+
            (?<month>$months):[\r\n]*
            (?<times>((\d{1,2}:\d{2}|TBD|TBC)\s+[^\r\n]+[\r\n]*)+)
            ~xsi";
    }

    /** @throws IFSCEventsScraperException */
    private function parseEventDetails(string $line): array
    {
        $regex = '~^
            (?<time>(\d{1,2}:\d{1,2}(?:\s+(?:AM|PM))?|TBC|TBD))\s+
            (?<name>[\w\'\-&\s]+)
            (?<url>\s*(http[^\s]+\s*)*)
            $~x';

        if (!preg_match($regex, trim($line), $match)) {
            throw new IFSCEventsScraperException("No event found in line: {$line}");
        }

        $startTime = $this->normalizer->normalizeTime($match['time']);
        $eventName = $this->normalizer->cupName($match['name']);
        $streamUrl = $this->normalizer->firstUrl($match['url'] ?? '');

        return [$startTime, $eventName, $streamUrl];
    }

    private function buildLeagueUri(int $id): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $id);
    }

    public function normalize(DOMElement $paragraph): string
    {
        return $this->normalizer->removeNonAsciiCharacters($paragraph->textContent);
    }
}
