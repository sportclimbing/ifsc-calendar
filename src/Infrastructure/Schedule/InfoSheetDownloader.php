<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Schedule;

use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpException;
use Symfony\Component\Filesystem\Filesystem;

final readonly class InfoSheetDownloader
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Filesystem $filesystem,
    ) {
    }

    /** @throws InfoSheetDownloadFailedException */
    public function downloadInfoSheet(string $infoSheetUrl): string
    {
        try {
            $tmpFile = $this->getTempFileName();
            $this->downloadFile($infoSheetUrl, $tmpFile);

            return $tmpFile;
        } catch (HttpException) {
            throw new InfoSheetDownloadFailedException(
                'Unable to download info sheet',
            );
        }
    }

    /** @throws HttpException */
    private function downloadFile(string $url, string $tmpFile): void
    {
        $this->httpClient->downloadFile($url, $tmpFile);
    }

    private function getTempFileName(): string
    {
        return $this->filesystem->tempnam('/tmp', 'infosheet_');
    }
}
