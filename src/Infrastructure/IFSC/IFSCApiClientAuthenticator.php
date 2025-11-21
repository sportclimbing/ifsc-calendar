<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\IFSC;

use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpException;

final readonly class IFSCApiClientAuthenticator
{
    public const string IFSC_SESSION_COOKIE_NAME = '_verticallife_resultservice_session';

    public const string IFSC_RESULTS_INFO_PAGE = 'https://ifsc.results.info/';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /** @throws IFSCApiClientException */
    public function fetchSessionId(): string
    {
        try {
            foreach ($this->getCookies() as $cookie) {
                $parsedCookie = $this->parseCookie($cookie);

                if (isset($parsedCookie[self::IFSC_SESSION_COOKIE_NAME])) {
                    return $this->extractSessionId($parsedCookie);
                }
            }
        } catch (HttpException) {
        }

        throw new IFSCApiClientException('Could not retrieve session cookie');
    }

    /**
     * @return string[]
     * @throws HttpException
     */
    private function getCookies(): array
    {
        $headers = self::IFSC_RESULTS_INFO_PAGE
            |> $this->httpClient->getHeaders(...)
            |> array_change_key_case(...);

        return $headers['set-cookie'] ?? [];
    }

    /** @return array<string,string> */
    private function parseCookie(string $cookie): array
    {
        parse_str($cookie, $result);

        return $result;
    }

    /** @param array<string,string> $result */
    private function extractSessionId(array $result): string
    {
        return sscanf($result[self::IFSC_SESSION_COOKIE_NAME], '%[^;]s')[0];
    }
}
