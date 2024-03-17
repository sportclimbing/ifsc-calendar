<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;
use Override;

final readonly class HttpGuzzleClient implements HttpClientInterface
{
    public function __construct(
        private Client $client,
    ) {
    }

    /** @inheritDoc */
    #[Override]
    public function getRetry(string $url, array $options = []): string
    {
        $retryCount = 0;
        $html = '';

        do {
            try {
                $html = $this->get($url, $options);
            } catch (HttpException $e) {
                if (++$retryCount > 5) {
                    throw new $e;
                }

                sleep(2);
            }
        } while (!$html);

        return $html;
    }

    /** @throws HttpException */
    #[Override]
    public function getHeaders(string $url): array
    {
        try {
            return $this->client->request('GET', $url)->getHeaders();
        } catch (GuzzleException $e) {
            throw new HttpException("Unable to retrieve HTTP headers: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /** @throws HttpException */
    private function get(string $url, array $options): string
    {
        try {
            return $this->client->request('GET', $url, $options)->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
