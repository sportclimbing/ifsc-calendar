<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\YouTube;

use DateTimeImmutable;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeApiClient;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideo;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideoCollection;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpGuzzleClient;
use SensitiveParameter;

final readonly class GuzzleYouTubeClient implements YouTubeApiClient
{
    private const MIN_VIDEO_AGE = '1/1/2023';

    private mixed $apiKey;

    public function __construct(
        private HttpGuzzleClient $client,
        private string $channelId,
        #[SensitiveParameter]
        mixed $apiKey,
    ) {
        // https://github.com/php/php-src/issues/9420
        $this->apiKey = $apiKey;
    }

    public function fetchRecentVideos(): YouTubeVideoCollection
    {
        $videoCollection = new YouTubeVideoCollection();

        foreach ($this->fetchLatestVideos() as $video) {
            $videoCollection->add($video);
        }

        return $videoCollection;
    }

    /** @return YouTubeVideo[] */
    private function fetchLatestVideos(): array
    {
        $minAge = new DateTimeImmutable(self::MIN_VIDEO_AGE);
        $nextPageToken = null;
        $items = [];

        do {
            $response = $this->getJsonResponse($nextPageToken);
            // $nextPageToken = $response->nextPageToken ?? null;

            foreach ($response->items as $item) {
                $youTubeVideo = $this->createVideo($item);

                if ($youTubeVideo->publishedAt < $minAge) {
                    break;
                }

                $items[] = $youTubeVideo;
            }
        } while (!empty($nextPageToken));

        return $items;
    }

    /** @throws Exception */
    private function buildApiUrl(?string $nextPageToken): string
    {
        return sprintf(
            'https://www.googleapis.com/youtube/v3/search?%s',
            $this->buildSearchParams($nextPageToken)
        );
    }

    public function buildSearchParams(?string $nextPageToken): string
    {
        $params = [
            'part' => 'snippet',
            'channelId' => $this->channelId,
            'key' => $this->getApiKey(),
            'type' => 'video',
            'order' => 'date',
            'regionCode' => 'US',
            'maxResults' => '50',
            //   'eventType' => 'completed',
        ];

        if ($nextPageToken) {
            $params['pageToken'] = $nextPageToken;
        }

        return http_build_query($params, arg_separator: '&');
    }

    private function getJsonResponse(?string $nextPageToken): object
    {
        try {
            $response = $this->client->get($this->buildApiUrl($nextPageToken));
        } catch (GuzzleException) {
            throw new Exception('Unable to obtain response from YouTube');
        }

        try {
            $jsonResponse = json_decode($response, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new Exception('Invalid JSON response from YouTube');
        }

        if (!is_iterable($jsonResponse->items)) {
            throw new Exception('Invalid API response from YouTube');
        }

        return $jsonResponse;
    }

    public function createVideo(object $item): YouTubeVideo
    {
        return new YouTubeVideo(
            title: html_entity_decode($item->snippet->title),
            description: html_entity_decode($item->snippet->description),
            videoId: $item->id->videoId,
            publishedAt: new DateTimeImmutable($item->snippet->publishedAt),
        );
    }

    private function getApiKey(): string
    {
        if (!$this->apiKey) {
            throw new Exception('Missing YOUTUBE_API_KEY env var');
        }

        return $this->apiKey;
    }
}
