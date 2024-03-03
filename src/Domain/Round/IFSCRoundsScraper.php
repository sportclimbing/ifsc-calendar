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

final readonly class IFSCRoundsScraper
{
    private const IFSC_EVENT_PAGE_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    public function __construct(
        private HttpClientInterface $client,
        private IFSCRoundFactory $roundFactory,
        private DOMHelper $domHelper,
        private Normalizer $normalizer,
    ) {
    }

    /**
     * @throws InvalidURLException
     * @throws IFSCEventsScraperException
     * @throws Exception
     */
    public function fetchRoundsAndPosterForEvent(IFSCSeasonYear $season, int $eventId, string $timeZone): IFSCScrapedEventsResult
    {
        /** @var IFSCSchedule[] $schedules */
        $schedules = [];
        $xpath = $this->getXPathForEventsWithId($eventId);
        $dateRegex = $this->buildDateRegex();
        [$startDate, $endDate] = $this->getDateRage($xpath);

        foreach ($this->domHelper->getParagraphs($xpath) as $paragraph) {
            if (!preg_match_all($dateRegex, $this->normalizeParagraph($paragraph), $matches)) {
                continue;
            }

            foreach ($matches['day'] as $key => $day) {
                foreach ($this->getNonEmptyLines($matches, $key) as $line) {
                    $schedules = $this->getSchedule(
                        name: $matches['month'][$key],
                        line: $line,
                        day: $day,
                        timeZone: $timeZone,
                        season: $season,
                        schedules: $schedules,
                    );
                }
            }
        }

        return new IFSCScrapedEventsResult(
            $this->dateWithTimezone($startDate, $timeZone),
            $this->dateWithTimezone($endDate, $timeZone),
            $this->domHelper->getPoster($xpath),
            $this->getRounds($schedules),
        );
    }

    /** @throws Exception */
    private function getXPathForEventsWithId(int $eventId): DOMXPath
    {
        return $this->domHelper->htmlToXPath(
            $this->client->getRetry($this->buildLeagueUri($eventId))
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

    private function getRounds(array $schedules): array
    {
        $rounds = [];

        foreach ($schedules as $schedule) {
            $rounds[] = $this->roundFactory->create(
                name: $schedule->cupName,
                streamUrl: $schedule->streamUrl,
                startTime: $schedule->duration->startTime,
                endTime: $schedule->duration->endTime,
            );
        }

        return $rounds;
    }

    /**
     * @throws InvalidURLException
     * @throws IFSCEventsScraperException
     */
    private function getSchedule(string $name, string $line, string $day, string $timeZone, IFSCSeasonYear $season, array $schedules): array
    {
        [$cupName, $eventTime, $streamUrl] = $this->parseEventDetails($line);
        $month = Month::fromName($name);

        $schedules[] = IFSCSchedule::create(
            day: (int) $day,
            month: $month,
            time: $eventTime,
            timeZone: $timeZone,
            season: $season,
            cupName: $cupName,
            streamUrl: $streamUrl,
        );

        return $schedules;
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

    private function buildLeagueUri(int $id): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $id);
    }

    private function normalizeParagraph(DOMElement $paragraph): string
    {
        return $this->normalizer->removeNonAsciiCharacters($paragraph->textContent);
    }

    private function getNonEmptyLines(array $matches, int $key): array
    {
        return $this->normalizer->nonEmptyLines($matches['times'][$key]);
    }

    private function createStartDate(string $day, string $month, string $year): DateTime
    {
        return DateTime::createFromFormat('d F Y H:i:s', "{$day} {$month} {$year} 08:00:00");
    }

    /** @throws Exception */
    private function dateWithTimezone(DateTime $date, string $timeZone): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable($date)->setTimezone(new DateTimeZone($timeZone));
    }
}
