<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Round;

use Exception;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetNotFoundEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\IFSCEventsScraperException;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundProviderInterface;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Infrastructure\Shell\Command;
use nicoSWD\IfscCalendar\Infrastructure\Shell\CommandFailedException;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetDownloader;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetScheduleParser;
use Override;
use Symfony\Component\Filesystem\Filesystem;

final readonly class InfoSheetRoundProvider implements IFSCRoundProviderInterface
{
    private const string INFO_SHEET_URL = 'https://ifsc.results.info/events/%d/infosheet';

    private const string COMMAND_PDF_TO_HTML = 'pdftohtml -noframes -i -stdout %s';

    public function __construct(
        private InfoSheetScheduleParser $scheduleProvider,
        private InfoSheetDownloader $downloader,
        private HttpClientInterface $httpClient,
        private Command $command,
        private EventDispatcherInterface $eventDispatcher,
        private Filesystem $filesystem,
    ) {
    }

    /** @inheritdoc */
    #[Override] public function fetchRounds(IFSCEventInfo $event): array
    {
        $infoSheetUrl = $this->getInfoSheetUrl($event);

        if ($infoSheetUrl === null) {
            $this->emitInfoSheetNotFoundEvent($event);

            return [];
        }

        return $this->fetchRoundsFromInfoSheet($event, $infoSheetUrl);
    }

    /** @return IFSCSchedule[] */
    #[Override] public function fetchRoundsFromInfoSheet(IFSCEventInfo $event, string $infoSheetUrl): array
    {
        try {
            $pdfPath = $this->downloader->downloadInfoSheet($infoSheetUrl);

            $html = $this->convertPdfToHtml($pdfPath);
            $this->deleteTempFile($pdfPath);

            return $this->scheduleProvider->parseSchedule($html, $event->timeZone);
        } catch (Exception) {
            $this->emitInfoSheetNotFoundEvent($event);
        }

        return [];
    }

    /** @throws IFSCEventsScraperException */
    private function convertPdfToHtml(string $pdfPath): string
    {
        try {
            return $this->command->exec(self::COMMAND_PDF_TO_HTML, [$pdfPath]);
        } catch (CommandFailedException $e) {
            throw new IFSCEventsScraperException(
                "Unable to convert PDF to HTML: {$e->getMessage()}"
            );
        }
    }

    private function getInfoSheetUrl(IFSCEventInfo $event): ?string
    {
        try {
            return $this->httpClient->getRedirectLocation(
                url: $this->buildInfoSheetUrl($event),
            );
        } catch (HttpException) {
            return null;
        }
    }

    private function deleteTempFile(string $pdfPath): void
    {
        $this->filesystem->remove($pdfPath);
    }

    private function buildInfoSheetUrl(IFSCEventInfo $event): string
    {
        return sprintf(self::INFO_SHEET_URL, $event->eventId);
    }

    private function emitInfoSheetNotFoundEvent(IFSCEventInfo $event): void
    {
        $this->eventDispatcher->dispatch(new InfoSheetNotFoundEvent($event->eventName));
    }
}
