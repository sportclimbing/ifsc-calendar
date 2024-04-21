<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\HTTPRequestFailedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use Override;

final readonly class HttpGuzzleClient implements HttpClientInterface
{
    public function __construct(
        private Client $client,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /** @inheritDoc */
    #[Override] public function getRetry(string $url, array $options = []): string
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

                $this->emitRequestFailedEvent($url, $e->getCode(), $retryCount);
                sleep(2);
            }
        } while (!$html);

        return $html;
    }

    /** @inheritdoc */
    #[Override] public function getHeaders(string $url, array $options = []): array
    {
        try {
            return $this->client->request('GET', $url, $options)->getHeaders();
        } catch (GuzzleException $e) {
            throw new HttpException("Unable to retrieve HTTP headers: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /** @inheritdoc */
    #[Override] public function get(string $url, array $options): string
    {
        try {
            return $this->client->request('GET', $url, $options)->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /** @inheritdoc */
    #[Override] public function downloadFile(string $url, string $saveAs): void
    {
        $this->get($url, ['sink' => $saveAs]);
    }

    private function emitRequestFailedEvent(string $url, int $errorCode, int $retryCount): void
    {
        $this->eventDispatcher->dispatch(new HTTPRequestFailedEvent($url, $errorCode, $retryCount));
    }
}
