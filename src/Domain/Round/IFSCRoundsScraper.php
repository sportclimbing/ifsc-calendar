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
use DOMXPath;
use Exception;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

final readonly class IFSCRoundsScraper
{
    private const string IFSC_EVENT_PAGE_URL = 'https://www.ifsc-climbing.org/events/%s';

    public function __construct(
        private HttpClientInterface $client,
        private IFSCRoundFactory $roundFactory,
        private DOMHelper $domHelper,
    ) {
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws Exception
     */
    public function fetchRoundsAndPosterForEvent(string $slug, DateTimeZone $timeZone): IFSCScrapedEventsResult
    {
        $xpath = $this->getXPathForEventWithSlug($slug);
        [$startDate, $endDate] = $this->getDateRage($xpath);

        return new IFSCScrapedEventsResult(
            startDate: $this->dateWithTimezone($startDate, $timeZone),
            endDate: $this->dateWithTimezone($endDate, $timeZone),
            poster: null,
            rounds: $this->createRounds([]),
        );
    }

    /** @throws Exception */
    private function getXPathForEventWithSlug(string $slug): DOMXPath
    {
        return $this->domHelper->htmlToXPath(
            $this->client->getRetry($this->buildEventUri($slug))
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

    /** @return DateTime[] */
    private function getDateRage(DOMXPath $xpath): array
    {
        $patterns = [
            // 08-10April 2024
            '~^(?<start_day>\d{2})-(?<end_day>\d{2})(?<start_month>[A-Z]+)\s(?<start_year>\d{4})$~i',
            // 15March 2024
            '~^(?<start_day>\d{2})(?<start_month>[A-Z]+)\s(?<start_year>\d{4})$~i',
            // 25July-11August 2024
            '~^(?<start_day>\d{2})(?<start_month>[A-Z]+)-(?<end_day>\d{2})(?<end_month>[A-Z]+)\s(?<start_year>\d{4})$~i',
        ];

        $range = $this->domHelper->getDateRange($xpath);

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $range, $dateRange)) {
                $dateRange['end_day'] ??= $dateRange['start_day'];
                $dateRange['end_month'] ??= $dateRange['start_month'];

                break;
            }
        }

        return [
            $this->createStartDate($dateRange['start_day'], $dateRange['start_month'], $dateRange['start_year']),
            $this->createStartDate($dateRange['end_day'], $dateRange['end_month'], $dateRange['start_year']),
        ];
    }

    private function buildEventUri(string $slug): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $slug);
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
