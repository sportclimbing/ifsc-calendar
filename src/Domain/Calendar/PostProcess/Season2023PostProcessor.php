<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\PostProcess;

use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DOMElement;
use DOMNodeList;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFactory;
use nicoSWD\IfscCalendar\Domain\Event\Month;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

final readonly class Season2023PostProcessor
{
    private const BERN_SCHEDULE_URL = 'https://www.ifsc-climbing.org/bern-2023/schedule';
    private const BERN_IFSC_EVENT_ID = 1301;
    private const BERN_IFSC_EVENT_DESCRIPTION = 'IFSC - Climbing World Championships (B,L,S,B&L) - Bern (SUI) 2023';
    private const BERN_2023_POSTER = 'https://ifsc.stream/img/posters/bern2023.jpg';

    public function __construct(
        private HttpClientInterface $httpClient,
        private IFSCEventFactory $eventFactory,
        private Normalizer $normalizer,
        private DOMHelper $DOMHelper,
    ) {
    }

    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     */
    public function process(array $events): array
    {
        // Add missing Bern events, which are listed on a separate page in an
        // entirely different format. Thanks, y'all.
        $bernEvents = array_filter($events, $this->isBernEvent());

        if (!$bernEvents) {
            $events = array_merge($events, $this->fetchBernEvents());
        }

        return $events;
    }

    /** @return IFSCEvent[] */
    private function fetchBernEvents(): array
    {
        $events = [];

        foreach ($this->fetchEventsFromHTML() as $event) {
            $eventLine = sscanf($this->normalizeEventLine($event), '%d %s || %d:%d %[^$]s');

            [$day, $month, $hour, $minute, $name] = $eventLine;

            $startDateTime = $this->createStartDate($day, $month, $hour, $minute);
            $endDateTime = $this->getEndDateTime($startDateTime);

            $events[] = $this->eventFactory->create(
                name: $this->normalizer->cupName($name),
                id: self::BERN_IFSC_EVENT_ID,
                description: self::BERN_IFSC_EVENT_DESCRIPTION,
                streamUrl: '',
                poster: self::BERN_2023_POSTER,
                startTime: $startDateTime,
                endTime: $endDateTime,
            );
        }

        return $events;
    }

    private function createStartDate(int $day, string $month, int $hour, int $minute): DateTimeImmutable
    {
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

    private function fetchEventsFromHTML(): DOMNodeList
    {
        $xpath = $this->DOMHelper->htmlToXPath(
            $this->httpClient->get(self::BERN_SCHEDULE_URL)
        );

        return $xpath->query("//div[contains(@class, 'js-filter')]/div[@data-tag]");
    }

    private function normalizeEventLine(DOMElement $event): string
    {
        return $this->normalizer->removeMultipleSpaces($event->textContent);
    }
}
