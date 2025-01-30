<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;

final readonly class YouTubeLiveStreamFinder implements YouTubeLiveStreamFinderInterface
{
    public function __construct(
        private YouTubeLinkMatcher $linkMatcher,
        private YouTubeVideoCollection $videoCollection,
    ) {
    }

    public function findLiveStream(IFSCEventInfo $event, string $roundName): LiveStream
    {
        return $this->linkMatcher->findStreamUrlForRound($event, $roundName, $this->videoCollection);
    }
}
