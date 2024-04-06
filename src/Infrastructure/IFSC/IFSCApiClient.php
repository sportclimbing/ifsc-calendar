<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\IFSC;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\RequestOptions;
use JsonException;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;

readonly class IFSCApiClient
{
    public function __construct(
        private string $sessionToken,
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @return object|array<mixed>
     * @throws IFSCApiClientException
     * @throws HttpException
     */
    public function authenticatedGet(string $url): object|array
    {
        return $this->request($url, [
            RequestOptions::HEADERS => [
                // Apparently, this is required to pass the authorization check
                'referer' => IFSCApiClientAuthenticator::IFSC_RESULTS_INFO_PAGE,
            ],
            RequestOptions::COOKIES => CookieJar::fromArray(
                cookies: [IFSCApiClientAuthenticator::IFSC_SESSION_COOKIE_NAME => $this->sessionToken],
                domain: 'ifsc.results.info',
            ),
        ]);
    }

    /**
     * @param array<string,mixed> $options
     * @return object|array<mixed>
     * @throws IFSCApiClientException
     * @throws HttpException
     */
    public function request(string $url, array $options = []): object|array
    {
        try {
            return @json_decode(
                json: $this->httpClient->getRetry($url, $options),
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new IFSCApiClientException(
                "Unable to parse JSON: {$e->getMessage()}"
            );
        }
    }
}
