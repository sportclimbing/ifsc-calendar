<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Schedule;

use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetNotFoundEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;

final readonly class PDFDownloader
{
    private const string INFO_SHEET_URL = 'https://ifsc.results.info/events/%d/infosheet';

    public function __construct(
        private HttpClientInterface $httpClient,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function downloadInfoSheet(object $event): ?string
    {
        try {
            $infoSheetUrl = $this->getInfoSheetUrl($event);

            if ($infoSheetUrl) {
                $tmpFile = $this->getTempFileName();
                $this->downloadFile($infoSheetUrl, $tmpFile);

                return $tmpFile;
            }
        } catch (HttpException) {
        }

        $this->emitInfoSheetNotFoundEvent($event);

        return null;
    }

    /** @throws HttpException */
    private function getInfoSheetUrl(object $event): ?string
    {
        $headers = array_change_key_case(
            $this->httpClient->getHeaders(
                url: $this->buildInfoSheetUrl($event),
                options: ['allow_redirects' => false],
            )
        );

        return $headers['location'][0] ?? null;
    }

    private function emitInfoSheetNotFoundEvent(object $event): void
    {
        $this->eventDispatcher->dispatch(new InfoSheetNotFoundEvent($event->event));
    }

    private function buildInfoSheetUrl(object $event): string
    {
        return sprintf(self::INFO_SHEET_URL, $event->event_id);
    }

    private function getTempFileName(): string
    {
        return tempnam('/tmp', 'infosheet_');
    }

    /** @throws HttpException */
    private function downloadFile(string $url, string $tmpFile): void
    {
        $this->httpClient->get($url, ['sink' => $tmpFile]);
    }
}