<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Events;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use GuzzleHttp\Client;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCGuzzleEventsScraper
{
    private const XPATH_PARAGRAPHS = "//*[@id='ifsc_event']/div/div/div[@class='text']/p";
    private const IFSC_EVENT_PAGE_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    private const WEEK_DAYS = [
        'MONDAY',
        'TUESDAY',
        'WEDNESDAY',
        'THURSDAY',
        'FRIDAY',
        'SATURDAY',
        'SUNDAY',
    ];
    private const MONTHS = [
        'JANUARY' => 1,
        'FEBRUARY' => 2,
        'MARCH' => 3,
        'APRIL' => 4,
        'MAY' => 5,
        'JUNE' => 6,
        'JULY' => 7,
        'AUGUST' => 8,
        'SEPTEMBER' => 9,
        'OCTOBER' => 10,
        'NOVEMBER' => 11,
        'DECEMBER' => 12,
    ];

    public function __construct(
        private Client $client,
    ) {
    }

    public function fetchEventsForLeague(int $season, int $eventId, string $timezone, string $eventName): array
    {
        $response = $this->client->request('GET', $this->buildLeagueUri($eventId))->getBody()->getContents();
        $paragraphs = $this->getParagraphs($response);
        $weekDays = implode('|', self::WEEK_DAYS);
        $months = implode('|', array_keys(self::MONTHS));
        $schedules = [];
        $events = [];

        $dateRegex = "~^(?:$weekDays),\s+(?<day>\d{1,2})\s+(?<month>$months)~";
        $timeRegx = '~^(?<time>\d{1,2}:\d{2})\s+(?<league>[^•]+)~u';

        foreach ($paragraphs as $paragraph) {
            if (preg_match($dateRegex, trim($paragraph->nodeValue), matches: $date)) {
                foreach ($paragraph->getElementsByTagName('span') as $span) {
                    if (preg_match($timeRegx, trim($span->nodeValue), matches: $time)) {
                        $schedules[] = [
                            'day'    => $date['day'],
                            'month'  => $date['month'],
                            'time'   => $time['time'],
                            'season' => $season,
                            'league' => $this->leagueName($time['league']),
                            'url'    => $this->getEventUrl($span),
                        ];
                    }
                }
            }
        }

        foreach ($schedules as $schedule) {
            $startDateTime = $this->getStartDateTime($schedule, $timezone);
            $endDateTime = $this->getEndDateTime($startDateTime);

            $events[] = new IFSCEvent(
                name: "IFSC: {$schedule['league']}",
                id: $eventId,
                description: $eventName,
                startTime: $startDateTime,
                endTime: $endDateTime,
            );
        }

        return $events;
    }

    public function getEventUrl(DOMNode $span): string
    {
        $links = $span->getElementsByTagName('a');

        if ($links->length > 0) {
            $url = (string) $links->item(0)->getAttribute('href');
        } else {
            $url = '';
        }

        return $url;
    }

    public function getParagraphs(string $response): DOMNodeList
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($response);
        $xpath = new DOMXPath($dom);

        return $xpath->query(self::XPATH_PARAGRAPHS);
    }

    public function getStartDateTime(array $schedule, string $timezone): DateTimeImmutable
    {
        [$hour, $minute] = explode(':', $schedule['time']);

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone($timezone));
        $date->setDate($schedule['season'], $this->monthNameToNumber($schedule['month']), (int) $schedule['day']);
        $date->setTime((int) $hour, (int) $minute);

        return DateTimeImmutable::createFromMutable($date);
    }

    public function getEndDateTime(DateTimeImmutable $date): DateTimeImmutable
    {
        $endDate = DateTime::createFromImmutable($date);
        $endDate->modify('+2 hours');

        return DateTimeImmutable::createFromMutable($endDate);
    }

    private function monthNameToNumber(string $month): int
    {
        return self::MONTHS[$month];
    }

    public function buildLeagueUri(int $id): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $id);
    }

    public function leagueName(string $league): string
    {
        return ucwords(strtolower(trim($league)));
    }
}
