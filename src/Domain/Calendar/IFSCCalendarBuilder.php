<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use nicoSWD\IfscCalendar\Domain\Calendar\Exceptions\NoEventsFoundException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkFetcher;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkMatcher;

final readonly class IFSCCalendarBuilder
{
    public function __construct(
        private IFSCCalendarBuilderFactory $calendarBuilderFactory,
        private IFSCEventFetcherInterface $eventFetcher,
        private YouTubeLinkFetcher $linkFetcher,
        private YouTubeLinkMatcher $linkMatcher,
        private IFSCCalendarPostProcess $calendarPostProcess,
    ) {
    }

    /** @throws NoEventsFoundException */
    public function generateForLeague(int $season, int $league, string $format, bool $fetchYouTubeUrls): string
    {
        $events = $this->calendarPostProcess->process(
            season: $season,
            events: $this->fetchEvents($season, $league),
        );

        if (empty($events)) {
            throw NoEventsFoundException::forLeague($league);
        }

        if ($fetchYouTubeUrls) {
            $this->fetchEventStreamUrls($events);
        }

        return $this->calendarBuilderFactory->generateForFormat($format, $events);
    }

    /** @param IFSCEvent[] $events */
    private function fetchEventStreamUrls(array &$events): void
    {
        $videoCollection = $this->linkFetcher->fetchRecentVideos();

        foreach ($events as &$event) {
            if ($event->streamUrl) {
                continue;
            }

            $streamUrl = $this->linkMatcher->findStreamUrlForEvent($event, $videoCollection);

            if ($streamUrl) {
                $event = $event->updateStreamUrl($streamUrl);
            }
        }
    }

    /** @return IFSCEvent[] */
    public function fetchEvents(int $season, int $league): array
    {
        return $this->eventFetcher->fetchEventsForLeague($season, $league);
    }
}
