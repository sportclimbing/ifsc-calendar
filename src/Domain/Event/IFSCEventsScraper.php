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
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

final readonly class IFSCEventsScraper
{
    private const XPATH_PARAGRAPHS = "//*[@id='ifsc_event']/div/div/div[@class='text']/p";

    private const XPATH_SIDEBAR = "//div[@class='text2']";

    private const IFSC_EVENT_PAGE_URL = 'https://www.ifsc-climbing.org/component/ifsc/?view=event&WetId=%d';

    private const POSTER_IMAGE_PREFIX = 'https://cdn.ifsc-climbing.org/images/Events/';

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    /** @throws IFSCEventsScraperException */
    public function fetchEventsForLeague(int $season, int $eventId, string $timezone, string $eventName): array
    {
        $xpath = $this->getXPathForEventsWithId($eventId);
        $paragraphs = $this->getParagraphs($xpath);
        $dateRegex = $this->buildDateRegex();
        $schedules = [];

        foreach ($paragraphs as $paragraph) {
            if (preg_match($dateRegex, trim($paragraph->nodeValue), matches: $date)) {
                foreach ($paragraph->getElementsByTagName('em') as $span) {
                    $currentEventName = $this->trim($span->nextSibling->nodeValue);
                    $time = $this->parseTimeFromSpan($span, $eventId);

                    $schedules[] = IFSCSchedule::create(
                        day: (int) $date['day'],
                        month: Month::fromName($date['month']),
                        time: $time,
                        season: $season,
                        league: $this->leagueName($currentEventName),
                        url: $this->getEventUrl($span->parentNode),
                    );
                }
            }
        }

        $poster = $this->getPoster($xpath);
        $events = [];

        foreach ($schedules as $schedule) {
            $startDateTime = $this->getStartDateTime($schedule, $timezone);
            $endDateTime = $this->getEndDateTime($startDateTime);

            $events[] = new IFSCEvent(
                name: $schedule->league,
                id: $eventId,
                description: $eventName,
                streamUrl: $schedule->url,
                poster: $poster,
                startTime: $startDateTime,
                endTime: $endDateTime,
            );
        }

        return $events;
    }

    private function getEventUrl(DOMNode $span): string
    {
        $links = $span->getElementsByTagName('a');

        if ($links->length > 0) {
            $url = (string) $links->item(0)->getAttribute('href');
        } else {
            $url = '';
        }

        return $url;
    }

    private function getXPathForEventsWithId(int $eventId): DOMXPath
    {
        $htmlResponse = $this->client->get($this->buildLeagueUri($eventId));
        $lastValue = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlResponse);

        libxml_use_internal_errors($lastValue);

        return new DOMXPath($dom);
    }

    private function getParagraphs(DOMXPath $xpath): DOMNodeList
    {
        return $xpath->query(self::XPATH_PARAGRAPHS);
    }

    private function getPoster(DOMXPath $xpath): string
    {
        $sideBar = $xpath->query(self::XPATH_SIDEBAR)->item(0);

        if (!$sideBar) {
            return '';
        }

        $images = $sideBar->getElementsByTagName('img');

        if (!is_iterable($images)) {
            return '';
        }

        foreach ($images as $image) {
            foreach ($image->attributes as $name => $attribute) {
                if ($name === 'data-src' && str_starts_with($attribute->textContent, self::POSTER_IMAGE_PREFIX)) {
                    return (string) $attribute->textContent;
                }
            }
        }

        return '';
    }

    private function getStartDateTime(IFSCSchedule $schedule, string $timezone): DateTimeImmutable
    {
        [$hour, $minute] = explode(':', $schedule->time);

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone($timezone));
        $date->setDate($schedule->season, $schedule->month->value, $schedule->day);
        $date->setTime((int) $hour, (int) $minute);

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
            ^(?:MONDAY|TUESDAY|WEDNESDAY|THURSDAY|FRIDAY|SATURDAY|SUNDAY),
            \s+(?<day>\d{1,2})
            \s+(?<month>$months)
            ~x";
    }

    /** @throws IFSCEventsScraperException */
    public function parseTimeFromSpan(DOMElement $span, int $eventId): string
    {
        $time = $this->trim($span->nodeValue);

        if (in_array($time, ['TBC', 'TBD'], strict: true)) {
            // set arbitrary time for now. It will eventually update automatically
            // once IFSC sets the correct time. Sometimes it's set to `TBC` or `TBD`
            $time = '8:00';
        } elseif (!preg_match('~^\d{1,2}:\d{2}$~', $time)) {
            throw IFSCEventsScraperException::timeParseExceptionForEventWithId($time, $eventId);
        }

        return $time;
    }

    private function buildLeagueUri(int $id): string
    {
        return sprintf(self::IFSC_EVENT_PAGE_URL, $id);
    }

    private function leagueName(string $league): string
    {
        return ucwords(strtolower(trim($league)));
    }

    private function trim(string $string): string
    {
        return preg_replace(['~^\W+~', '~\W+$~'], '', trim($string));
    }
}
