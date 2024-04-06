<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

readonly final class YouTubeLiveStreamFinderFactory
{
    public function __construct(
        private YouTubeLinkMatcher   $linkMatcher,
        private YouTubeVideoProvider $linkFetcher,
    ) {
    }

    public function create(): YouTubeLiveStreamFinder
    {
        return new YouTubeLiveStreamFinder(
            $this->linkMatcher,
            $this->linkFetcher->fetchAllVideos(),
        );
    }
}
