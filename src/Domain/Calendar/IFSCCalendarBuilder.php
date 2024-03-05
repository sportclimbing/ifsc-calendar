<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Exception;
use nicoSWD\IfscCalendar\Domain\Calendar\Exceptions\NoEventsFoundException;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Stream\IFSCStreamUrl;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkFetcher;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkMatcher;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideoCollection;

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

    /**
     * @throws NoEventsFoundException
     * @throws Exception
     */
    public function generateForLeague(IFSCSeasonYear $season, int $leagueId, IFSCCalendarFormat $format): string
    {
        $events = $this->calendarPostProcess->process(
            season: $season,
            events: $this->fetchEvents($season, $leagueId),
        );

        if (empty($events)) {
            throw NoEventsFoundException::forLeague($leagueId);
        }

        $this->fetchEventStreamUrls($events, $season);

        return $this->calendarBuilderFactory->generateForFormat($format, $events);
    }

    /**
     * @param IFSCEvent[] $events
     * @throws InvalidURLException
     */
    private function fetchEventStreamUrls(array &$events, IFSCSeasonYear $season): void
    {
        $videoCollection = $this->linkFetcher->fetchRecentVideos($season);

        foreach ($events as $event) {
            foreach ($event->rounds as $round) {
                if (!$round->streamUrl->hasUrl()) {
                    $round->streamUrl = $this->searchStreamUrl($round, $event, $videoCollection);
                }
            }
        }
    }

    /** @return IFSCEvent[] */
    private function fetchEvents(IFSCSeasonYear $season, int $leagueId): array
    {
        return $this->eventFetcher->fetchEventsForLeague($season, $leagueId);
    }

    /** @throws InvalidURLException */
    private function searchStreamUrl(
        IFSCRound $round,
        IFSCEvent $event,
        YouTubeVideoCollection $videoCollection
    ): IFSCStreamUrl {
        return new IFSCStreamUrl($this->linkMatcher->findStreamUrlForRound($round, $event, $videoCollection));
    }
}
