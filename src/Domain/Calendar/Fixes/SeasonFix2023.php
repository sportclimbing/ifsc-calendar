<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\Fixes;

use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFactory;
use nicoSWD\IfscCalendar\Domain\Event\Month;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

final readonly class SeasonFix2023
{
    private const BERN_SCHEDULE_URL = 'https://www.ifsc-climbing.org/bern-2023/schedule';

    public function __construct(
        private HttpClientInterface $httpClient,
        private IFSCEventFactory $eventFactory,
        private Normalizer $normalizer,
    ) {
    }

    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     */
    public function fix(array $events): array
    {
        // Add missing Bern events, which are listed on a separate page in an
        // entirely different format. Thanks, y'all.
        $bernEvents = array_filter($events, $this->isBernEvent());

        if (!$bernEvents) {
            $events = array_merge($events, $this->fetchBernEvents());
        }

        return $events;
    }

    private function fetchBernEvents(): array
    {
        // Use DOM/XPath
        $regex = '~<div data-tag=[^>]+>\s*(?:<div[^>]+>\s*){2}\s*(?<date>\d{1,2}\sAUGUST\s\|\|\s\d{1,2}:\d{2})\s*</div>\s*<h3[^>]+>(?<name>[^<]+)~s';
        $html = $this->httpClient->get(self::BERN_SCHEDULE_URL);
        $events = [];

        if (!preg_match_all($regex, $html, $matches)) {
            return [];
        }

        foreach ($matches['date'] as $key => $date) {
            $startDateTime = $this->createStartDate($date);
            $endDateTime = $this->getEndDateTime($startDateTime);

            $events[] = $this->eventFactory->create(
                name: $this->normalizer->cupName($matches['name'][$key]),
                id: 1301,
                description: 'IFSC - Climbing World Championships (B,L,S,B&L) - Bern (SUI) 2023',
                streamUrl: '',
                poster: 'https://ifsc.stream/img/posters/bern2023.jpg',
                startTime: $startDateTime,
                endTime: $endDateTime,
            );
        }

        return $events;
    }

    private function createStartDate(string $date): DateTimeImmutable
    {
        [$day, $month, $hour, $minute] = sscanf(trim($date), '%d %s || %d:%d');

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('Europe/Zurich'));
        $date->setDate(2023, Month::fromName($month)->value, $day);
        $date->setTime($hour, $minute);

        return DateTimeImmutable::createFromMutable($date);
    }

    private function getEndDateTime(DateTimeImmutable $date): DateTimeImmutable
    {
        $endDate = DateTime::createFromImmutable($date);
        $endDate->modify('+3 hours');

        return DateTimeImmutable::createFromMutable($endDate);
    }

    private function isBernEvent(): Closure
    {
        return static fn(IFSCEvent $event): bool => str_contains($event->name, 'Bern (SUI)');
    }
}
