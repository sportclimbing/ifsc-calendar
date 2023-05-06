<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFetcherInterface;
use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkFetcher;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkMatcher;

final readonly class IFSCCalendarBuilder
{
    public function __construct(
        private IFSCCalendarBuilderFactory $calendarBuilderFactory,
        private IFSCEventFetcherInterface $eventFetcher,
        private YouTubeLinkFetcher $linkFetcher,
        private YouTubeLinkMatcher $linkMatcher,
    ) {
    }

    /**
     * @param int $season
     * @param IFSCLeague[] $leagues
     * @param string $format
     * @param bool $fetchYouTubeUrls
     * @return string
     * @throws Exception
     */
    public function generateForLeagues(int $season, array $leagues, string $format, bool $fetchYouTubeUrls): string
    {
        $events = [];

        foreach ($leagues as $league) {
            $leagueEvents = $this->eventFetcher->fetchEventsForLeague($season, $league);

            if (empty($leagueEvents)) {
                throw new Exception("No events found for league '{$league->name}'");
            }

            $events += $leagueEvents;
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
}
