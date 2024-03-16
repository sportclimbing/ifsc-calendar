<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Stream;

use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;

final class StreamUrl
{
    private const string YOUTUBE_BASE_URL = 'https://youtu.be/%s';

    private const string YOUTUBE_THUMB_URL = 'https://img.youtube.com/vi/%s/0.jpg';

    private const string REGEX_YOUTUBE_ID = '~youtu(\.be|be\.com)/(live/|watch\?v=)?(?<video_id>[a-zA-Z0-9_-]{10,})~';

    public readonly ?string $url;

    /** @throws InvalidURLException */
    public function __construct(?string $url = null) {
        if ($url !== null) {
            $this->url = $this->normalizeStreamUrl($url);
        } else {
            $this->url = null;
        }
    }

    public function hasUrl(): bool
    {
        return $this->url !== null;
    }

    public function youTubeThumbUrl(): ?string
    {
        if ($this->hasUrl()) {
            $videoId = $this->getVideoId($this->url);

            if ($videoId) {
                return sprintf(self::YOUTUBE_THUMB_URL, $videoId);
            }
        }

        return null;
    }

    /** @throws InvalidURLException */
    private function normalizeStreamUrl(string $streamUrl): ?string
    {
        $this->assertValidUrl($streamUrl);
        $videoId = $this->getVideoId($streamUrl);

        if ($videoId) {
            return sprintf(self::YOUTUBE_BASE_URL, $videoId);
        }

        return $streamUrl;
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
