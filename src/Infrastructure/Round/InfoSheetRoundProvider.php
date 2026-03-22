<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Round;

use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetDownloadFailedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetNotFoundEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetParsingFailedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundProviderInterface;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetChatGptScheduleParserException;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetDownloadFailedException;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetChatGptScheduleParser;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetDownloader;
use Override;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

final readonly class InfoSheetRoundProvider implements IFSCRoundProviderInterface
{
    private const string INFO_SHEET_URL = 'https://ifsc.results.info/events/%d/infosheet';

    public function __construct(
        private InfoSheetChatGptScheduleParser $scheduleProvider,
        private InfoSheetDownloader $downloader,
        private HttpClientInterface $httpClient,
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
        if ($event->isParaClimbing()) {
            return [];
        }

        $infoSheetHeaders = $this->getInfoSheetHeaders($infoSheetUrl);

        $cachedSchedules = $this->fetchCachedSchedules(
            event: $event,
            infoSheetUrl: $infoSheetUrl,
            infoSheetHeaders: $infoSheetHeaders,
        );

        if ($cachedSchedules !== null) {
            return $cachedSchedules;
        }

        $pdfPath = $this->downloadInfoSheetOrEmitFailure($event, $infoSheetUrl);

        if ($pdfPath === null) {
            return [];
        }

        return $this->parseDownloadedInfoSheet(
            event: $event,
            infoSheetUrl: $infoSheetUrl,
            infoSheetHeaders: $infoSheetHeaders,
            pdfPath: $pdfPath,
        );
    }

    /**
     * @param array<array<string>> $infoSheetHeaders
     * @return IFSCSchedule[]|null
     */
    private function fetchCachedSchedules(
        IFSCEventInfo $event,
        string $infoSheetUrl,
        array $infoSheetHeaders,
    ): ?array {
        return $this->scheduleProvider->loadCachedSchedule(
            event: $event,
            infoSheetUrl: $infoSheetUrl,
            infoSheetHeaders: $infoSheetHeaders,
        );
    }

    private function downloadInfoSheetOrEmitFailure(IFSCEventInfo $event, string $infoSheetUrl): ?string
    {
        try {
            return $this->downloader->downloadInfoSheet($infoSheetUrl);
        } catch (InfoSheetDownloadFailedException $e) {
            $this->emitInfoSheetDownloadFailedEvent($event, $e);
        } catch (Throwable $e) {
            $this->emitInfoSheetDownloadFailedEvent($event, $e);
        }

        return null;
    }

    /**
     * @param array<array<string>> $infoSheetHeaders
     * @return IFSCSchedule[]
     */
    private function parseDownloadedInfoSheet(
        IFSCEventInfo $event,
        string $infoSheetUrl,
        array $infoSheetHeaders,
        string $pdfPath,
    ): array {
        try {
            return $this->scheduleProvider->parseScheduleFromPdf(
                event: $event,
                pdfPath: $pdfPath,
                infoSheetUrl: $infoSheetUrl,
                infoSheetHeaders: $infoSheetHeaders,
            );
        } catch (InfoSheetChatGptScheduleParserException $e) {
            $this->emitInfoSheetParsingFailedEvent($event, $e);
        } catch (Throwable $e) {
            $this->emitInfoSheetParsingFailedEvent($event, $e);
        } finally {
            $this->deleteTempFile($pdfPath);
        }

        return [];
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

    /** @return array<array<string>> */
    private function getInfoSheetHeaders(string $infoSheetUrl): array
    {
        try {
            return $this->httpClient->getHeaders($infoSheetUrl);
        } catch (HttpException) {
            return [];
        } catch (Throwable) {
            return [];
        }
    }

    private function deleteTempFile(string $pdfPath): void
    {
        try {
            $this->filesystem->remove($pdfPath);
        } catch (Throwable) {
        }
    }

    private function buildInfoSheetUrl(IFSCEventInfo $event): string
    {
        return sprintf(self::INFO_SHEET_URL, $event->eventId);
    }

    private function emitInfoSheetNotFoundEvent(IFSCEventInfo $event): void
    {
        $this->eventDispatcher->dispatch(new InfoSheetNotFoundEvent($event->eventName));
    }

    private function emitInfoSheetDownloadFailedEvent(IFSCEventInfo $event, Throwable $e): void
    {
        $this->eventDispatcher->dispatch(new InfoSheetDownloadFailedEvent(
            eventName: $event->eventName,
            reason: $e->getMessage(),
        ));
    }

    private function emitInfoSheetParsingFailedEvent(IFSCEventInfo $event, Throwable $e): void
    {
        $this->eventDispatcher->dispatch(new InfoSheetParsingFailedEvent(
            eventName: $event->eventName,
            reason: $e->getMessage(),
        ));
    }
}
