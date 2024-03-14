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

    private const string REGEX_YOUTUBE_ID = '~youtu(\.be|be\.com)/(live/|watch\?v=)?(?<video_id>[a-zA-Z0-9_-]{10,})~';

    public readonly ?string $url;

    /** @throws InvalidURLException */
    public function __construct(?string $url = null) {
        $this->assertValidUrl($url);
        $this->url = $this->normalizeStreamUrl($url);
    }

    public function hasUrl(): bool
    {
        return $this->url !== null;
    }

    /** @throws InvalidURLException */
    private function assertValidUrl(?string $streamUrl): void
    {
        if ($streamUrl !== null && !filter_var($streamUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidURLException("Invalid URL: {$streamUrl}");
        }
    }

    private function normalizeStreamUrl(?string $streamUrl): ?string
    {
        if ($streamUrl !== null && preg_match(self::REGEX_YOUTUBE_ID, $streamUrl, $match)) {
            return sprintf(self::YOUTUBE_BASE_URL, $match['video_id']);
        }

        return $streamUrl;
    }
}
