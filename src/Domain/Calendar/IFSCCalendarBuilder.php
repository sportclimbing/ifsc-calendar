<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Exception;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\NoRoundsForEventFoundEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
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
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param int[] $leagueIds
     * @param IFSCCalendarFormat[] $formats
     * @throws InvalidURLException
     * @throws Exception
     */
    public function generateForLeague(IFSCSeasonYear $season, array $leagueIds, array $formats): array
    {
        $events = [];

        foreach ($leagueIds as $leagueId) {
            $leagueEvents = $this->calendarPostProcess->process(
                season: $season,
                events: $this->fetchEvents($season, $leagueId),
            );

            $this->fetchEventStreamUrls($leagueEvents, $season);
            $this->appendEventsWithRounds($events, $leagueEvents);
        }

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
    private function fetchEvents(IFSCSeasonYear $season, int $leagueId): array
    {
        return $this->eventFetcher->fetchEventsForLeague($season, $leagueId);
    }

    private function searchStreamUrl(
        IFSCRound $round,
        IFSCEvent $event,
        YouTubeVideoCollection $videoCollection,
    ): StreamUrl {
        return $this->linkMatcher->findStreamUrlForRound($round, $event, $videoCollection);
    }

    /**
     * @param IFSCEvent[] $events
     * @param IFSCEvent[] $leagueEvents
     */
    private function appendEventsWithRounds(array &$events, array $leagueEvents): void
    {
        $filteredEvents = [];

        foreach ($leagueEvents as $event) {
            if (empty($event->rounds)) {
                $this->emitNoRoundsFoundWarning($event);
            } else {
                $filteredEvents[] = $event;
            }
        }

        $events = array_merge($events, $filteredEvents);
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

    private function emitNoRoundsFoundWarning(IFSCEvent $event): void
    {
        $this->eventDispatcher->dispatch(new NoRoundsForEventFoundEvent($event->eventName));
    }
}
