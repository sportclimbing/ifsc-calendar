<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\IFSC;

use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;

final readonly class IFSCApiClientAuthenticator
{
    public const string IFSC_SESSION_COOKIE_NAME = '_verticallife_resultservice_session';

    public const string IFSC_RESULTS_INFO_PAGE = 'https://ifsc.results.info/';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @throws IFSCApiClientException
     * @throws HttpException
     */
    public function fetchSessionId(): string
    {
        foreach ($this->getCookies() as $cookie) {
            $parsedCookie = $this->parseCookie($cookie);

            if (isset($parsedCookie[self::IFSC_SESSION_COOKIE_NAME])) {
                return $this->extractSessionId($parsedCookie);
            }
        }

        throw new IFSCApiClientException('Could not retrieve session cookie');
    }

    /**
     * @return string[]
     * @throws HttpException
     */
    private function getCookies(): array
    {
        $headers = $this->httpClient->getHeaders(self::IFSC_RESULTS_INFO_PAGE);

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
