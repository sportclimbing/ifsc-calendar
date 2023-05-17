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

final readonly class HttpGuzzleClient implements HttpClientInterface
{
    public function __construct(
        private Client $client,
    ) {
    }

    /** @throws GuzzleException */
    public function get(string $url): string
    {
        return $this->client->request('GET', $url)->getBody()->getContents();
    }
}