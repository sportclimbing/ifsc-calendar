<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\PostProcess;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DOMNode;
use Exception;
use Iterator;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Stream\IFSCStreamUrl;

final readonly class Season2024PostProcessor
{
    private const int OLYMPIC_GAMES_EVENT_ID = 1386;

    private const string OLYMPIC_GAMES_PARIS_URL = 'https://www.ifsc-climbing.org/olympic-games';

    private const string TIMEZONE_PARIS = 'Europe/Paris';

    private const string XPATH_SCHEDULE_NODE = '//div[contains(@class, "uk-child-width-1-2")]/*/*/*/p';

    // Eg: Thursday, 8 August - 10:00 AM to 1:15 PM
    private const string REGEX_EVENT_DATE = '~^(?<day>[a-z]+, \d{1,2} [a-z]+)\s?- (?<start_time>\d{1,2}:\d{1,2} [AP]M) to (?<end_time>\d{1,2}:\d{1,2} [AP]M):$~i';

    public function __construct(
        private IFSCRoundFactory $roundFactory,
        private HttpClientInterface $httpClient,
        private DOMHelper $domHelper,
    ) {
    }

    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     * @throws Exception
     */
    public function process(array $events): array
    {
        foreach ($events as $event) {
            if ($this->isOlympicGamesEvent($event)) {
                $event->rounds = $this->fetchOlympicGamesRounds();
            }
        }

        return $events;
    }

    /**
     * @throws IFSCEventsScraperException
     * @throws Exception
     */
    private function fetchOlympicGamesRounds(): array
    {
        $rounds = [];

        foreach ($this->getNonEmptyNodes() as $node) {
            [$name, $startTime, $endTime] = $this->parseNameAndRoundSchedule($node);
            $roundNames = $this->getRoundNames($name);

            if (count($roundNames) === 1) {
                $rounds[] = $this->createRound($name, $startTime, $endTime);
            } else {
                $avgRoundDuration = $this->averageRoundDuration($startTime, $endTime);

                $rounds[] = $this->createRound1($roundNames, $startTime, $endTime, $avgRoundDuration);
                $rounds[] = $this->createRound2($roundNames, $startTime, $endTime, $avgRoundDuration);
            }
        }

        return $rounds;
    }

    /** @throws Exception */
    private function createRound(string $name, DateTime $startTime, DateTime $endTime): IFSCRound
    {
        return $this->roundFactory->create(
            name: ucwords($name),
            streamUrl: new IFSCStreamUrl(),
            startTime: DateTimeImmutable::createFromMutable($startTime),
            endTime: DateTimeImmutable::createFromMutable($endTime),
        );
    }

    /**
     * @return array{string,DateTime,DateTime}
     * @throws IFSCEventsScraperException
     */
    private function parseNameAndRoundSchedule(DOMNode $node): array
    {
        [$time, $name] = explode("\n", $node->nodeValue, limit: 2);

        return [
            $this->normalizeRoundName($name),
            ...$this->parseStartAndEndTime($time),
        ];
    }

    /**
     * @return DateTime[]
     * @throws IFSCEventsScraperException
     */
    private function parseStartAndEndTime(string $time): array
    {
        if (preg_match(self::REGEX_EVENT_DATE, $time, $match)) {
            $startDate = $this->createDateFromMatch($match['day'], $match['start_time']);
            $endDate = $this->createDateFromMatch($match['day'], $match['end_time']);

            return [$startDate, $endDate];
        }

        throw new IFSCEventsScraperException("Unable to parse string from time: {$time}");
    }

    private function normalizeRoundName(string $name): string
    {
        return preg_replace(
            ["~[\r\n]+~", '~\s{2,}~', '~(boulder|lead) round ~i'],
            ['', ' ', ''],
            trim($name),
        );
    }

    private function createDateFromMatch(string $day, string $time): DateTime
    {
        return DateTime::createFromFormat(
            format: 'l, d F H:i A',
            datetime: "{$day} {$time}",
            timezone: new DateTimeZone(self::TIMEZONE_PARIS)
        );
    }

    private function getNonEmptyNodes(): Iterator
    {
        $xpath = $this->domHelper->htmlToXPath(
            $this->httpClient->getRetry(self::OLYMPIC_GAMES_PARIS_URL),
        );

        $nodeList = $xpath->query(self::XPATH_SCHEDULE_NODE);

        for ($index = 1; $index < $nodeList->count(); $index++) {
            $item = $nodeList->item($index);

            if (strlen($item->textContent) > 10) {
                yield $item;
            }
        }
    }

    private function isOlympicGamesEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::OLYMPIC_GAMES_EVENT_ID;
    }

    private function averageRoundDuration(DateTime $startTime, DateTime $endTime): int
    {
        $diff = $startTime->diff($endTime);

        return (int) ceil(($diff->m + ($diff->h * 60)) / 2);
    }

    private function getRoundNames(string $name): array
    {
        return preg_split('~\s*,\s+~', $name, limit: 2, flags: PREG_SPLIT_NO_EMPTY);
    }

    private function calcEndTime(DateTime $endTime, int $avg): DateTime
    {
        return $endTime->modify("-{$avg} minutes");
    }

    private function calcStartTime(DateTime $startTime, int $avg): DateTime
    {
        return $startTime->modify("+{$avg} minutes");
    }

    /** @throws Exception */
    private function createRound1(array $roundNames, DateTime $startTime, DateTime $endTime, int $avgRoundDuration): IFSCRound
    {
        return $this->createRound($roundNames[0], $startTime, $this->calcEndTime($endTime, $avgRoundDuration));
    }

    /** @throws Exception */
    private function createRound2(array $roundNames, DateTime $startTime, DateTime $endTime, int $avgRoundDuration): IFSCRound
    {
        return $this->createRound($roundNames[1], $this->calcStartTime($startTime, $avgRoundDuration), $endTime);
    }
}
