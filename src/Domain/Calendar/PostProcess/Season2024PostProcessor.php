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
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;

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
     * @return IFSCRound[]
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
    private function createRound(
        string $name,
        DateTime $startTime,
        DateTime $endTime,
        IFSCRoundStatus $status = IFSCRoundStatus::CONFIRMED
    ): IFSCRound {
        return $this->roundFactory->create(
            name: ucwords($name),
            streamUrl: new StreamUrl(),
            startTime: DateTimeImmutable::createFromMutable($startTime),
            endTime: DateTimeImmutable::createFromMutable($endTime),
            status: $status,
        );
    }

    /**
     * @return array{string,DateTime,DateTime}
     * @throws IFSCEventsScraperException
     */
    private function parseNameAndRoundSchedule(DOMNode $node): array
    {
        [$time, $name] = explode("\n", $node->nodeValue, limit: 2);

        return [$name, ...$this->parseStartAndEndTime($time)];
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

    /** @throws Exception */
    private function createRound1(array $roundNames, DateTime $startTime, DateTime $endTime, int $avgRoundDuration): IFSCRound
    {
        return $this->createRound(
            name: $roundNames[0],
            startTime: $startTime,
            endTime: $this->calcEndTime($endTime, $avgRoundDuration),
        );
    }

    /** @throws Exception */
    private function createRound2(array $roundNames, DateTime $startTime, DateTime $endTime, int $avgRoundDuration): IFSCRound
    {
        return $this->createRound(
            name: $roundNames[1],
            startTime: $this->calcStartTime($startTime, $avgRoundDuration),
            endTime: $endTime,
            status: IFSCRoundStatus::ESTIMATED,
        );
    }

    private function calcEndTime(DateTime $endTime, int $avgRoundDuration): DateTime
    {
        return $endTime->modify("-{$avgRoundDuration} minutes");
    }

    private function calcStartTime(DateTime $startTime, int $avgRoundDuration): DateTime
    {
        return $startTime->modify("+{$avgRoundDuration} minutes");
    }

    /** @return string[] */
    private function getRoundNames(string $name): array
    {
        $callback = static function (string $name): string {
            $regex = '~, (Boulder|Lead) Round~i';

            if (preg_match($regex, $name, $match)) {
                $name = match ($match[1]) {
                    'Boulder' => str_replace(' & Lead', '', $name),
                    'Lead' => str_replace('Boulder & ', '', $name),
                };

                $name = preg_replace($regex, '', $name);
            }

            return trim($name);
        };

        return array_filter(
            array_map($callback,
                explode("\n", $name, limit: 2)
            )
        );
    }
}
