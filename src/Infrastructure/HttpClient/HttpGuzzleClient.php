<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\HttpClient;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

final readonly class HttpGuzzleClient implements HttpClientInterface
{
    public function __construct(
        private Client $client,
    ) {
    }

    /** @throws GuzzleException */
    public function getRetry(string $url, array $options = []): string
    {
        $retryCount = 0;
        $html = '';

        do {
            try {
                $html = $this->get($url, $options);
            } catch (Exception $e) {
                if (++$retryCount > 5) {
                    throw $e;
                }

                sleep(2);
            }
        } while (!$html);

        return $html;
    }

    /** @throws GuzzleException */
    public function getHeaders(string $url): array
    {
        return $this->client->request('GET', $url)->getHeaders();
    }

    /** @throws GuzzleException */
    private function get(string $url, array $options): string
    {
        return $this->client->request('GET', $url, $options)->getBody()->getContents();
    }
}
