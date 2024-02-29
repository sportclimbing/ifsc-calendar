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
        private IFSCCalendarPostProcess $calendarPostProcess,
        private YouTubeLinkFetcher $linkFetcher,
        private YouTubeLinkMatcher $linkMatcher,
    ) {
    }

    /** @throws NoEventsFoundException */
    public function generateForLeague(int $season, int $league, string $format): string
    {
        $events = $this->calendarPostProcess->process(
            season: $season,
            events: $this->fetchEvents($season, $league),
        );

        if (empty($events)) {
            throw NoEventsFoundException::forLeague($league);
        }

        $this->fetchEventStreamUrls($events, $season);

        return $this->calendarBuilderFactory->generateForFormat($format, $events);
    }

    /** @param IFSCEvent[] $events */
    private function fetchEventStreamUrls(array &$events, int $season): void
    {
        $videoCollection = $this->linkFetcher->fetchRecentVideos($season);

        foreach ($events as $event) {
            foreach ($event->rounds as &$round) {
                if ($round->streamUrl) {
                    continue;
                }

                $streamUrl = $this->linkMatcher->findStreamUrlForRound($round, $event, $videoCollection);

                if ($streamUrl) {
                    $round = $round->updateStreamUrl($streamUrl);
                }
            }
        }
    }

    /** @return IFSCEvent[] */
    private function fetchEvents(int $season, int $league): array
    {
        return $this->eventFetcher->fetchEventsForLeague($season, $league);
    }
}
