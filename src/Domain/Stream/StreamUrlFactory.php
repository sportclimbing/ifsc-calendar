<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Stream;

use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeApiClient;

final readonly class StreamUrlFactory
{
    private const string YOUTUBE_BASE_URL = 'https://youtu.be/%s';

    private const string REGEX_YOUTUBE_ID = '~youtu(\.be|be\.com)/(live/|watch\?v=)?(?<video_id>[a-zA-Z0-9_-]{10,})~';

    public function __construct(
        private YouTubeApiClient $apiClient,
    ) {
    }

    /** @throws InvalidURLException */
    public function create(?string $streamUrl, array $restrictedRegions = []): StreamUrl
    {
        if ($streamUrl !== null) {
            $this->assertValidUrl($streamUrl);

            if (!$restrictedRegions) {
                $videoId = $this->getVideoId($streamUrl);

                if ($videoId) {
                    $streamUrl = sprintf(self::YOUTUBE_BASE_URL, $videoId);
                    $restrictedRegions = $this->apiClient->fetchRestrictedRegionsForVideo($videoId);
                }
            }
        }

        return new StreamUrl(
            url: $streamUrl,
            restrictedRegions: $restrictedRegions,
        );
    }

    private function getVideoId(string $url): ?string
    {
        if (preg_match(self::REGEX_YOUTUBE_ID, $url, $match)) {
            return $match['video_id'];
        }

        return null;
    }

    /** @throws InvalidURLException */
    private function assertValidUrl(string $streamUrl): void
    {
        if (!filter_var($streamUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidURLException("Invalid URL: {$streamUrl}");
        }
    }
}
