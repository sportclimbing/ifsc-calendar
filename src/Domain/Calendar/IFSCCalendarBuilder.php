<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Exception;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventSorter;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;
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
        private IFSCEventSorter $eventSorter,
    ) {
    }

    /**
     * @param IFSCCalendarFormat[] $formats
     * @throws InvalidURLException
     * @throws Exception
     */
    public function generateForSeason(IFSCSeasonYear $season, array $leagues, array $formats): array
    {
        $events = $this->calendarPostProcess->process(
            season: $season,
            events: $this->fetchEvents($season, $leagues),
        );

        $this->fetchEventStreamUrls($events, $season);
        $this->eventSorter->sortByDate($events);

        return $this->buildCalendars($formats, $events);
    }

    /** @param IFSCEvent[] $events */
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
    private function fetchEvents(IFSCSeasonYear $season, array $leagues): array
    {
        return $this->eventFetcher->fetchEventsForSeason($season, $leagues);
    }

    private function searchStreamUrl(
        IFSCRound $round,
        IFSCEvent $event,
        YouTubeVideoCollection $videoCollection,
    ): StreamUrl {
        return $this->linkMatcher->findStreamUrlForRound($round, $event, $videoCollection);
    }

    /**
     * @param IFSCCalendarFormat[] $formats
     * @param IFSCEvent[] $events
     */
    private function buildCalendars(array $formats, array $events): array
    {
        $results = [];

        foreach ($formats as $format) {
            $results[$format->value] = $this->calendarBuilderFactory->generateForFormat($format, $events);
        }

        return $results;
    }
}
