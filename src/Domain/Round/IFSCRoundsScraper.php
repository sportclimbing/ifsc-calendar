<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Exception;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use nicoSWD\IfscCalendar\Domain\Event\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
use nicoSWD\IfscCalendar\Domain\Event\Month;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrlFactory;

final readonly class IFSCRoundsScraper
{
    private const string IFSC_EVENT_PAGE_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    public function __construct(
        private HttpClientInterface $client,
        private IFSCRoundFactory $roundFactory,
        private DOMHelper $domHelper,
        private Normalizer $normalizer,
        private StreamUrlFactory $streamUrlFactory,
    ) {
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws Exception
     */
    public function fetchRoundsAndPosterForEvent(IFSCSeasonYear $season, int $eventId, DateTimeZone $timeZone): IFSCScrapedEventsResult
    {
        /** @var IFSCSchedule[] $schedules */
        $schedules = [];
        $xpath = $this->getXPathForEventWithId($eventId);

        foreach ($this->getParagraphs($xpath) as $paragraph) {
            $schedule = $this->getSchedule($paragraph);

            if (!$schedule) {
                continue;
            }

            foreach ($schedule['day'] as $key => $day) {
                foreach ($this->getNonEmptyLines($schedule, $key) as $line) {
                    $schedules[] = $this->createSchedule(
                        name: $schedule['month'][$key],
                        line: $line,
                        day: $day,
                        timeZone: $timeZone,
                        season: $season,
                    );
                }
            }
        }

        [$startDate, $endDate] = $this->getDateRage($xpath);

        return new IFSCScrapedEventsResult(
            $this->dateWithTimezone($startDate, $timeZone),
            $this->dateWithTimezone($endDate, $timeZone),
            $this->domHelper->getPoster($xpath),
            $this->createRounds($schedules),
        );
    }

    /** @throws Exception */
    private function getXPathForEventWithId(int $eventId): DOMXPath
    {
        return $this->domHelper->htmlToXPath(
            $this->client->getRetry($this->buildEventUri($eventId))
        );
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws InvalidURLException
     */
    private function parseEventDetails(string $line): IFSCRoundsScrapedResult
    {
        $regex = '~^
            (?<time>(\d{1,2}:\d{1,2}(?:\s+(?:AM|PM))?|TBC|TBD))\s+
            (?<name>[\w\'\-&\s]+)
            (?<url>\s*(http[^\s]+\s*)*)
            $~x';

        if (!preg_match($regex, trim($line), $match)) {
            throw new IFSCEventsScraperException("No event found in line: {$line}");
        }

        $roundName = $this->normalizer->cupName($match['name']);
        $startTime = $this->normalizer->normalizeTime($match['time']);
        $streamUrl = $this->normalizer->firstUrl($match['url'] ?? null);

        return new IFSCRoundsScrapedResult(
            roundName: $roundName,
            startTime: $startTime,
            streamUrl: $this->streamUrlFactory->create($streamUrl),
        );
    }

    /**
     * @param IFSCSchedule[] $schedules
     * @return IFSCRound[]
     */
    private function createRounds(array $schedules): array
    {
        $rounds = [];

        foreach ($schedules as $schedule) {
            $rounds[] = $this->roundFactory->create(
                name: $schedule->roundName,
                streamUrl: $schedule->streamUrl,
                startTime: $schedule->duration->startTime,
                endTime: $schedule->duration->endTime,
                status: IFSCRoundStatus::CONFIRMED,
            );
        }

        return $rounds;
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws InvalidURLException
     */
    private function createSchedule(string $name, string $line, string $day, DateTimeZone $timeZone, IFSCSeasonYear $season): IFSCSchedule
    {
        $eventDetails = $this->parseEventDetails($line);
        $month = Month::fromName($name);

        return IFSCSchedule::create(
            day: (int) $day,
            month: $month,
            time: $eventDetails->startTime,
            timeZone: $timeZone,
            season: $season,
            roundName: $eventDetails->roundName,
            streamUrl: $eventDetails->streamUrl,
        );
    }

    private function getSchedule(DOMElement $paragraph): ?array
    {
        $dateRegex = "~
            (?:MONDAY|TUESDAY|WEDNESDAY|THURSDAY|FRIDAY|SATURDAY|SUNDAY),\s+
            (?<day>\d{1,2})\s+
            (?<month>JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER):[\r\n]*
            (?<times>((\d{1,2}:\d{2}|TBD|TBC)\s+[^\r\n]+[\r\n]*)+)
            ~xsi";

        if (preg_match_all($dateRegex, $this->normalizeParagraph($paragraph), $matches)) {
            return $matches;
        }

        return null;
    }

    /**
     * @return DateTime[]
     * @throws IFSCEventsScraperException
     */
    private function getDateRage(DOMXPath $xpath): array
    {
        $regex = '~
            ^(?<start_day>\d{2})
            (?:\s+(?<start_month>[A-Z]+))?
            \s+-\s+
            (?<end_day>\d{2})\s+
            (?<end_month>[A-Z]+)\s+
            (?<year>20\d{2})$~ix';

        if (!preg_match($regex, $this->domHelper->getDateRange($xpath), matches: $dateRange, flags: PREG_UNMATCHED_AS_NULL)) {
            throw new IFSCEventsScraperException('Unable to find date range');
        }

        $dateRange['start_month'] ??= $dateRange['end_month'];

        return [
            $this->createStartDate($dateRange['start_day'], $dateRange['start_month'], $dateRange['year']),
            $this->createStartDate($dateRange['end_day'], $dateRange['end_month'], $dateRange['year']),
        ];
    }

    private function buildEventUri(int $eventId): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $eventId);
    }

    private function normalizeParagraph(DOMElement $paragraph): string
    {
        return $this->normalizer->removeNonAsciiCharacters($paragraph->textContent);
    }

    private function getNonEmptyLines(array $matches, int $key): array
    {
        return $this->normalizer->nonEmptyLines($matches['times'][$key]);
    }

    private function getParagraphs(DOMXPath $xpath): DOMNodeList
    {
        return $this->domHelper->getParagraphs($xpath);
    }

    private function createStartDate(string $day, string $month, string $year): DateTime
    {
        return DateTime::createFromFormat('d F Y H:i:s', "{$day} {$month} {$year} 08:00:00");
    }

    /** @throws Exception */
    private function dateWithTimezone(DateTime $date, DateTimeZone $timeZone): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable($date)->setTimezone($timeZone);
    }
}
