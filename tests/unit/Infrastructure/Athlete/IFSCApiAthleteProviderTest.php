<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Infrastructure\Athlete;

use GuzzleHttp\RequestOptions;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteException;
use SportClimbing\IfscCalendar\Infrastructure\Athlete\IFSCApiAthleteProvider;
use SportClimbing\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;
use SportClimbing\IfscCalendar\Infrastructure\HttpClient\HttpException;
use SportClimbing\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCApiAthleteProviderTest extends TestCase
{
    private const string VALID_ATHLETE_PAYLOAD = <<<'JSON'
{
  "id": 13021,
  "firstname": "Annie",
  "lastname": "SANDERS",
  "birthday": "2007-07-22",
  "gender": "female",
  "personal_story": "story",
  "federation": {
    "id": 20,
    "name": "USA Climbing",
    "abbreviation": "USAC"
  },
  "country": "USA",
  "flag_url": "https://flags/USA.png",
  "city": "Fort Worth",
  "age": 18,
  "height": 161,
  "instagram": "https://www.instagram.com/annie.climbs.sanders/",
  "nickname": "Annie",
  "spoken_languages": "English",
  "photo_url": "https://photos/profile",
  "action_photo_url": "https://photos/action",
  "discipline_podiums": [
    {
      "discipline_kind": "lead",
      "total": 6,
      "1": 2,
      "2": 1,
      "3": 3
    }
  ],
  "world_championships_discipline_podiums": [
    {
      "discipline_kind": "lead",
      "total": 0,
      "1": 0,
      "2": 0,
      "3": 0
    }
  ],
  "continental_championships_discipline_podiums": [
    {
      "discipline_kind": "lead",
      "total": 0,
      "1": 0,
      "2": 0,
      "3": 0
    }
  ],
  "all_results": [
    {
      "season": "2025",
      "rank": 1,
      "discipline": "lead",
      "event_name": "IFSC World Cup Comunidad de Madrid 2025",
      "event_id": 1454,
      "event_location": "Madrid, ESP",
      "d_cat": 5,
      "date": "2025-07-19",
      "category_name": "Women",
      "result_url": "/api/v1/events/1454/result/5"
    }
  ],
  "cup_rankings": [
    {
      "name": "IFSC Climbing World Cup 2025",
      "id": 103,
      "season": "2025",
      "lead": {
        "rank": 4,
        "result_url": "/api/v1/cups/103/dcat/1",
        "d_cat_id": 1,
        "disc_kind_id": 0
      },
      "boulder": {
        "rank": 3,
        "result_url": "/api/v1/cups/103/dcat/3",
        "d_cat_id": 3,
        "disc_kind_id": 2
      }
    }
  ]
}
JSON;

    private const string MINIMAL_ATHLETE_PAYLOAD = <<<'JSON'
{
  "id": 13021,
  "firstname": "Annie",
  "lastname": "SANDERS",
  "gender": "female",
  "country": "USA",
  "flag_url": "https://flags/USA.png",
  "discipline_podiums": [],
  "world_championships_discipline_podiums": [],
  "continental_championships_discipline_podiums": [],
  "all_results": [],
  "cup_rankings": []
}
JSON;

    private const string LEAD_ONLY_CUP_RANKING_PAYLOAD = <<<'JSON'
{
  "id": 13021,
  "firstname": "Annie",
  "lastname": "SANDERS",
  "gender": "female",
  "country": "USA",
  "flag_url": "https://flags/USA.png",
  "discipline_podiums": [],
  "world_championships_discipline_podiums": [],
  "continental_championships_discipline_podiums": [],
  "all_results": [],
  "cup_rankings": [
    {
      "name": "IFSC Climbing World Cup 2025",
      "id": 103,
      "season": "2025",
      "lead": {
        "rank": 4,
        "result_url": "/api/v1/cups/103/dcat/1",
        "d_cat_id": 1,
        "disc_kind_id": 0
      }
    }
  ]
}
JSON;

    #[Test] public function athlete_info_is_fetched_from_expected_endpoint(): void
    {
        $httpClient = new class (self::VALID_ATHLETE_PAYLOAD) implements HttpClientInterface {
            public string $requestedUrl = '';

            /** @var array<string,mixed> */
            public array $requestedOptions = [];

            public function __construct(
                private string $response,
            ) {
            }

            #[Override] public function getRetry(string $url, array $options = []): string
            {
                $this->requestedUrl = $url;
                $this->requestedOptions = $options;

                return $this->response;
            }

            #[Override] public function getHeaders(string $url, array $options = []): array
            {
                return [];
            }

            #[Override] public function get(string $url, array $options): string
            {
                return '';
            }

            #[Override] public function getRedirectLocation(string $url): ?string
            {
                return null;
            }

            #[Override] public function downloadFile(string $url, string $saveAs): void
            {
            }
        };

        $provider = new IFSCApiAthleteProvider(
            new IFSCApiClient(sessionToken: 'token', httpClient: $httpClient),
        );

        $athlete = $provider->fetchAthlete(13021);

        $this->assertSame('https://ifsc.results.info/api/v1/athletes/13021', $httpClient->requestedUrl);
        $this->assertArrayHasKey(RequestOptions::HEADERS, $httpClient->requestedOptions);
        $this->assertArrayHasKey(RequestOptions::COOKIES, $httpClient->requestedOptions);
        $this->assertSame('https://ifsc.results.info/', $httpClient->requestedOptions[RequestOptions::HEADERS]['referer']);
        $this->assertSame(13021, $athlete->id);
        $this->assertSame('Annie', $athlete->firstName);
        $this->assertSame('SANDERS', $athlete->lastName);
        $this->assertSame('USAC', $athlete->federation->abbreviation);
        $this->assertSame(2, $athlete->disciplinePodiums[0]->firstPlace);
        $this->assertSame(1454, $athlete->allResults[0]->eventId);
        $this->assertSame(4, $athlete->cupRankings[0]->lead->rank);
    }

    #[Test] public function invalid_payload_throws_domain_exception(): void
    {
        $provider = new IFSCApiAthleteProvider(
            new IFSCApiClient(
                sessionToken: 'token',
                httpClient: new class () implements HttpClientInterface {
                    #[Override] public function getRetry(string $url, array $options = []): string
                    {
                        return '[]';
                    }

                    #[Override] public function getHeaders(string $url, array $options = []): array
                    {
                        return [];
                    }

                    #[Override] public function get(string $url, array $options): string
                    {
                        return '';
                    }

                    #[Override] public function getRedirectLocation(string $url): ?string
                    {
                        return null;
                    }

                    #[Override] public function downloadFile(string $url, string $saveAs): void
                    {
                    }
                },
            ),
        );

        $this->expectException(IFSCAthleteException::class);
        $this->expectExceptionMessage('Invalid athlete payload received from API');

        $provider->fetchAthlete(13021);
    }

    #[Test] public function optional_fields_can_be_missing(): void
    {
        $provider = new IFSCApiAthleteProvider(
            new IFSCApiClient(
                sessionToken: 'token',
                httpClient: new class (self::MINIMAL_ATHLETE_PAYLOAD) implements HttpClientInterface {
                    public function __construct(
                        private string $response,
                    ) {
                    }

                    #[Override] public function getRetry(string $url, array $options = []): string
                    {
                        return $this->response;
                    }

                    #[Override] public function getHeaders(string $url, array $options = []): array
                    {
                        return [];
                    }

                    #[Override] public function get(string $url, array $options): string
                    {
                        return '';
                    }

                    #[Override] public function getRedirectLocation(string $url): ?string
                    {
                        return null;
                    }

                    #[Override] public function downloadFile(string $url, string $saveAs): void
                    {
                    }
                },
            ),
        );

        $athlete = $provider->fetchAthlete(13021);

        $this->assertNull($athlete->nickname);
        $this->assertNull($athlete->spokenLanguages);
        $this->assertNull($athlete->photoUrl);
        $this->assertNull($athlete->actionPhotoUrl);
        $this->assertNull($athlete->birthday);
        $this->assertNull($athlete->personalStory);
        $this->assertNull($athlete->federation);
        $this->assertNull($athlete->city);
        $this->assertNull($athlete->age);
        $this->assertNull($athlete->height);
        $this->assertNull($athlete->instagram);
    }

    #[Test] public function cup_ranking_can_have_missing_boulder(): void
    {
        $provider = new IFSCApiAthleteProvider(
            new IFSCApiClient(
                sessionToken: 'token',
                httpClient: new class (self::LEAD_ONLY_CUP_RANKING_PAYLOAD) implements HttpClientInterface {
                    public function __construct(
                        private string $response,
                    ) {
                    }

                    #[Override] public function getRetry(string $url, array $options = []): string
                    {
                        return $this->response;
                    }

                    #[Override] public function getHeaders(string $url, array $options = []): array
                    {
                        return [];
                    }

                    #[Override] public function get(string $url, array $options): string
                    {
                        return '';
                    }

                    #[Override] public function getRedirectLocation(string $url): ?string
                    {
                        return null;
                    }

                    #[Override] public function downloadFile(string $url, string $saveAs): void
                    {
                    }
                },
            ),
        );

        $athlete = $provider->fetchAthlete(13021);

        $this->assertCount(1, $athlete->cupRankings);
        $this->assertSame(4, $athlete->cupRankings[0]->lead?->rank);
        $this->assertNull($athlete->cupRankings[0]->boulder);
    }

    #[Test] public function http_failures_are_mapped_to_domain_exception(): void
    {
        $provider = new IFSCApiAthleteProvider(
            new IFSCApiClient(
                sessionToken: 'token',
                httpClient: new class () implements HttpClientInterface {
                    #[Override] public function getRetry(string $url, array $options = []): string
                    {
                        throw new HttpException('IFSC API is unavailable');
                    }

                    #[Override] public function getHeaders(string $url, array $options = []): array
                    {
                        return [];
                    }

                    #[Override] public function get(string $url, array $options): string
                    {
                        return '';
                    }

                    #[Override] public function getRedirectLocation(string $url): ?string
                    {
                        return null;
                    }

                    #[Override] public function downloadFile(string $url, string $saveAs): void
                    {
                    }
                },
            ),
        );

        $this->expectException(IFSCAthleteException::class);
        $this->expectExceptionMessage('Unable to fetch athlete info: IFSC API is unavailable');

        $provider->fetchAthlete(13021);
    }
}
