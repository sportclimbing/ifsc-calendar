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
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use SebastianBergmann\CodeCoverage\ParserException;

final readonly class IFSCRoundsScraper
{
    private const string IFSC_EVENT_PAGE_URL = 'https://www.ifsc-climbing.org/events/%s';

    public function __construct(
        private HttpClientInterface $client,
        private IFSCRoundFactory $roundFactory,
        private DOMHelper $domHelper,
    ) {
    }

    /** @throws Exception */
    public function fetchRoundsAndPosterForEvent(string $slug, DateTimeZone $timeZone): IFSCScrapedEventsResult
    {
        $xpath = $this->getXPathForEventWithSlug($slug);
        [$startDate, $endDate] = $this->getDateRage($xpath, $timeZone);

        return new IFSCScrapedEventsResult(
            startDate: $this->immutableDateTime($startDate),
            endDate: $this->immutableDateTime($endDate),
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
    private function getDateRage(DOMXPath $xpath, DateTimeZone $timeZone): array
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

        if (empty($dateRange)) {
            throw new ParserException('Unable to parse date range');
        }

        return [
            $this->createStartDate($dateRange['start_day'], $dateRange['start_month'], $dateRange['start_year'], $timeZone),
            $this->createStartDate($dateRange['end_day'], $dateRange['end_month'], $dateRange['start_year'], $timeZone),
        ];
    }

    private function buildEventUri(string $slug): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $slug);
    }

    private function createStartDate(string $day, string $month, string $year, DateTimeZone $timeZone): DateTime
    {
        $dateTime = DateTime::createFromFormat('d F Y H:i:s', "{$day} {$month} {$year} 08:00:00", $timeZone);

        if (!$dateTime) {
            throw new ParserException('Unable to create start date');
        }

        return $dateTime;
    }

    /** @throws Exception */
    private function immutableDateTime(DateTime $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable($date);
    }
}
