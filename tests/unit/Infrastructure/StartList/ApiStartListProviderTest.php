<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Infrastructure\StartList;

use GuzzleHttp\RequestOptions;
use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStarter;
use SportClimbing\IfscCalendar\Domain\StartList\IFSCStartListException;
use SportClimbing\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;
use SportClimbing\IfscCalendar\Infrastructure\HttpClient\HttpException;
use SportClimbing\IfscCalendar\Infrastructure\IFSC\IFSCApiClient;
use SportClimbing\IfscCalendar\Infrastructure\StartList\ApiStartListProvider;

final class ApiStartListProviderTest extends TestCase
{
    private const string REGISTRATIONS_PAYLOAD = <<<'JSON'
[
  {
    "athlete_id": 1001,
    "firstname": "Alice",
    "lastname": "CONFIRMED",
    "country": "USA",
    "d_cats": [
      {"id": 3, "name": "BOULDER Men", "status": "confirmed"}
    ]
  },
  {
    "athlete_id": 1002,
    "firstname": "Bob",
    "lastname": "ABSENT",
    "country": "GBR",
    "d_cats": [
      {"id": 3, "name": "BOULDER Men", "status": "not attending"}
    ]
  },
  {
    "athlete_id": 1003,
    "firstname": "Cara",
    "lastname": "MIXED",
    "country": "FRA",
    "d_cats": [
      {"id": 3, "name": "BOULDER Men", "status": "not attending"},
      {"id": 5, "name": "LEAD Women", "status": "confirmed"}
    ]
  },
  {
    "athlete_id": 1004,
    "firstname": "Dana",
    "lastname": "NO_STATUS",
    "country": "ITA",
    "d_cats": [
      {"id": 5, "name": "LEAD Women"}
    ]
  },
  {
    "athlete_id": 1005,
    "firstname": "Eve",
    "lastname": "NULL_STATUS",
    "country": "AUT",
    "d_cats": [
      {"id": 7, "name": "BOULDER Women", "status": null}
    ]
  }
]
JSON;

    #[Test] public function athletes_without_explicit_not_attending_status_are_included(): void
    {
        $httpClient = new class (self::REGISTRATIONS_PAYLOAD) implements HttpClientInterface {
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

        $provider = new ApiStartListProvider(
            new IFSCApiClient(sessionToken: 'token', httpClient: $httpClient),
        );

        $startList = $provider->fetchStartListForEvent(1355);

        $this->assertSame('https://ifsc.results.info/api/v1/events/1355/registrations', $httpClient->requestedUrl);
        $this->assertArrayHasKey(RequestOptions::HEADERS, $httpClient->requestedOptions);
        $this->assertArrayHasKey(RequestOptions::COOKIES, $httpClient->requestedOptions);
        $this->assertSame('https://ifsc.results.info/', $httpClient->requestedOptions[RequestOptions::HEADERS]['referer']);
        $this->assertSame([1001, 1003, 1004, 1005], array_map(static fn (IFSCStarter $starter): int => $starter->athleteId, $startList));
    }

    #[Test] public function http_failures_are_mapped_to_domain_exception(): void
    {
        $provider = new ApiStartListProvider(
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

        $this->expectException(IFSCStartListException::class);
        $this->expectExceptionMessage('Unable to fetch start list: IFSC API is unavailable');

        $provider->fetchStartListForEvent(1355);
    }
}
