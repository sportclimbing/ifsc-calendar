<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\PostProcess;

use Closure;
use DOMElement;
use DOMNodeList;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFactory;
use nicoSWD\IfscCalendar\Domain\Event\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Event\Month;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

final readonly class Season2023PostProcessor
{
    private const BERN_SCHEDULE_URL = 'https://www.ifsc-climbing.org/bern-2023/schedule';
    private const BERN_XPATH_EVENTS = "//div[contains(@class, 'js-filter')]/div[@data-tag]";
    private const BERN_IFSC_EVENT_ID = 1301;
    private const BERN_IFSC_EVENT_DESCRIPTION = 'IFSC World Championships Bern 2023';
    private const BERN_IFSC_2023_POSTER = 'https://ifsc.stream/img/posters/bern2023.jpg';
    private const BERN_TIMEZONE = 'Europe/Zurich';
    private const BERN_IDENTIFIER = 'Bern (SUI)';

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
        if (!$this->hasBernEvents($events)) {
            $events = array_merge($events, $this->fetchBernEvents());
        }

        return $events;
    }

    /** @return IFSCEvent[] */
    private function fetchBernEvents(): array
    {
        $events = [];

        foreach ($this->fetchEventsFromHTML() as $event) {
            $schedule = $this->createSchedule(
                sscanf($this->normalizeEventLine($event), '%d %s || %d:%d %[^$]s')
            );

            $events[] = $this->eventFactory->create(
                name: $this->normalizer->cupName($schedule->cupName),
                id: self::BERN_IFSC_EVENT_ID,
                description: self::BERN_IFSC_EVENT_DESCRIPTION,
                streamUrl: $this->extractStreamUrl($event),
                poster: self::BERN_IFSC_2023_POSTER,
                startTime: $schedule->duration->startTime,
                endTime: $schedule->duration->endTime,
            );
        }

        return $events;
    }

    private function createSchedule(array $eventLine): IFSCSchedule
    {
        [$day, $month, $hour, $minute, $cupName] = $eventLine;

        return IFSCSchedule::create(
            day: $day,
            month: Month::fromName($month),
            time: "$hour:$minute",
            timeZone: self::BERN_TIMEZONE,
            season: 2023,
            cupName: $cupName,
        );
    }

    private function fetchEventsFromHTML(): DOMNodeList
    {
        $xpath = $this->DOMHelper->htmlToXPath(
            $this->httpClient->get(self::BERN_SCHEDULE_URL)
        );

        return $xpath->query(self::BERN_XPATH_EVENTS);
    }

    private function normalizeEventLine(DOMElement $event): string
    {
        return $this->normalizer->removeMultipleSpaces(
            explode("\n", $event->nodeValue)[0]
        );
    }

    /** @param IFSCEvent[] $events */
    private function hasBernEvents(array $events): bool
    {
        return count(array_filter($events, $this->isBernEvent())) > 0;
    }

    private function isBernEvent(): Closure
    {
        return static fn(IFSCEvent $event): bool => str_contains($event->name, self::BERN_IDENTIFIER);
    }

    private function extractStreamUrl(DOMElement $event): string
    {
        return $this->normalizer->normalizeStreamUrl(
            explode("\n", $event->textContent)[1]
        );
    }
}
