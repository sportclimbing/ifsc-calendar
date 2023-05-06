<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\YouTube;

use DateTimeImmutable;
use Exception;
use GuzzleHttp\Client;
use JsonException;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeApiClient;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideo;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideoCollection;

final readonly class GuzzleYouTubeClient implements YouTubeApiClient
{
    public function __construct(
        private Client $client,
        private string $channelId,
    ) {
    }

    public function fetchRecentVideos(): YouTubeVideoCollection
    {
        $videoCollection = new YouTubeVideoCollection();

        foreach ($this->fetchLatestVideos() as $video) {
            $videoCollection->add(new YouTubeVideo(
                title: html_entity_decode($video->snippet->title),
                description: html_entity_decode($video->snippet->description),
                videoId: $video->id->videoId,
                publishedAt: new DateTimeImmutable($video->snippet->publishedAt),
            ));
        }

        return $videoCollection;
    }

    private function fetchLatestVideos(): array
    {
        $response = $this->client->request('GET', $this->buildApiUrl())->getBody()->getContents();

        try {
            $jsonResponse = json_decode($response, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new Exception('Invalid JSON response from YouTube');
        }

        if (!is_iterable($jsonResponse->items)) {
            throw new Exception('Invalid API response from YouTube');
        }

        return $jsonResponse->items;
    }

    /** @throws Exception */
    private function buildApiUrl(): string
    {
        return sprintf(
            'https://www.googleapis.com/youtube/v3/search?%s',
            $this->buildSearchParams()
        );
    }

    public function buildSearchParams(): string
    {
        return http_build_query([
            'part' => 'snippet',
            'channelId' => $this->channelId,
            'key' => $this->getApiKey(),
            'type' => 'video',
            'order' => 'date',
            'regionCode' => 'US',
            'maxResults' => '50',
            //   'eventType' => 'completed',
        ], arg_separator: '&');
    }

    private function getApiKey(): string
    {
        $youtubeApiKey = getenv('YOUTUBE_API_KEY');

        if (!$youtubeApiKey) {
            throw new Exception('Missing YOUTUBE_API_KEY env var');
        }

        return (string) $youtubeApiKey;
    }
}
