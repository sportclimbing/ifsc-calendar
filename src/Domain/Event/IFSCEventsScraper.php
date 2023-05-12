<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

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
    public function fetchEventsForLeague(int $season, int $eventId, string $timeZone, string $eventName): array
    {
        $xpath = $this->getXPathForEventsWithId($eventId);
        $dateRegex = $this->buildDateRegex();
        /** @var IFSCSchedule[] $schedules */
        $schedules = [];

        foreach ($this->domHelper->getParagraphs($xpath) as $paragraph) {
            if (!preg_match_all($dateRegex, $this->normalizeParagraph($paragraph), $matches)) {
                continue;
            }

            foreach ($matches['day'] as $key => $day) {
                foreach ($this->normalizer->nonEmptyLines($matches['times'][$key]) as $line) {
                    $month = Month::fromName($matches['month'][$key]);

                    [$cupName, $eventTime, $streamUrl] = $this->parseEventDetails($line);

                    $schedules[] = IFSCSchedule::create(
                        day: (int) $day,
                        month: $month,
                        time: $eventTime,
                        timeZone: $timeZone,
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
            $events[] = $this->eventFactory->create(
                name: $schedule->cupName,
                id: $eventId,
                description: $eventName,
                streamUrl: $schedule->streamUrl,
                poster: $poster,
                startTime: $schedule->duration->startTime,
                endTime: $schedule->duration->endTime,
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
        $cupName = $this->normalizer->cupName($match['name']);
        $streamUrl = $this->normalizer->firstUrl($match['url'] ?? '');

        return [$cupName, $startTime, $streamUrl];
    }

    private function buildLeagueUri(int $id): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $id);
    }

    public function normalizeParagraph(DOMElement $paragraph): string
    {
        return $this->normalizer->removeNonAsciiCharacters($paragraph->textContent);
    }
}
