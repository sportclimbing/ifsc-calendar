<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\IFSC;

use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;

final readonly class IFSCApiClientFactory
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private IFSCApiClientAuthenticator $authenticator,
    ) {
    }

    /** @throws IFSCApiClientException */
    public function __invoke(): IFSCApiClient
    {
        return new IFSCApiClient(
            sessionToken: $this->authenticator->fetchSessionId(),
            httpClient: $this->httpClient,
        );
    }
}
