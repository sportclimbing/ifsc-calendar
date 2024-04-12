<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Round;

use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundProviderInterface;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetDownloader;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetScheduleProvider;
use Override;

final readonly class InfoSheetRoundProvider implements IFSCRoundProviderInterface
{
    public function __construct(
        private InfoSheetScheduleProvider $scheduleProvider,
        private InfoSheetDownloader $downloader,
    ) {
    }

    /**
     * @inheritdoc
     * @throws IFSCEventsScraperException
     */
    #[Override] public function fetchRounds(IFSCEventInfo $event): array
    {
        $pdfPath = $this->downloader->downloadInfoSheet($event);

        if ($pdfPath) {
            $html = $this->convertPdfToHtml($pdfPath);
            $this->deleteTempFile($pdfPath);

            return $this->scheduleProvider->parseSchedule($html, $event->timeZone);
        }

        return [];
    }

    /** @throws IFSCEventsScraperException */
    private function convertPdfToHtml(string $pdfPath): string
    {
        $process = $this->execPdfToHtml($pdfPath, $pipes);

        if (!is_resource($process)) {
            throw new IFSCEventsScraperException('Unable to convert PDF to HTML');
        }

        $html = stream_get_contents($pipes[1]);

        if (empty($html)) {
            throw new IFSCEventsScraperException("No HTML returned by 'pdftohtml'");
        }

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new IFSCEventsScraperException("'pdftohtml' exited with code {$exitCode}");
        }

        return $html;
    }

    private function execPdfToHtml(string $pdfPath, mixed &$pipes): mixed
    {
        $pdfPath = escapeshellarg($pdfPath);

        return proc_open(
            command: "pdftohtml -noframes -i -stdout {$pdfPath}",
            descriptor_spec: $this->getDescriptorSpec(),
            pipes: $pipes,
            cwd: '/tmp',
            env_vars: [],
        );
    }

    /** @return array<int,array<string>> */
    private function getDescriptorSpec(): array
    {
        return [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', '/tmp/error-output.txt', 'a'],
        ];
    }

    private function deleteTempFile(string $pdfPath): void
    {
        unlink($pdfPath);
    }
}
