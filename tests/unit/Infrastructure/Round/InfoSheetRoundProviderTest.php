<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\tests\Infrastructure\Round;

use DateTimeZone;
use GuzzleHttp\Client;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetDownloadFailedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\Event\InfoSheetParsingFailedEvent;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Infrastructure\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Infrastructure\Round\InfoSheetRoundProvider;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetChatGptScheduleParser;
use nicoSWD\IfscCalendar\Infrastructure\Schedule\InfoSheetDownloader;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class InfoSheetRoundProviderTest extends TestCase
{
    #[Test]
    public function parser_exception_emits_parsing_failed_event(): void
    {
        $previousApiKey = $this->setEnv('OPENAI_API_KEY', null);
        $previousModel = $this->setEnv('OPENAI_MODEL', null);

        try {
            $provider = new InfoSheetRoundProvider(
                scheduleProvider: new InfoSheetChatGptScheduleParser(
                    httpClient: new Client(),
                    scheduleFactory: $this->createScheduleFactory(),
                    eventDispatcher: $this->createSilentEventDispatcher(),
                ),
                downloader: new InfoSheetDownloader(
                    httpClient: $this->createDownloadSuccessHttpClient(),
                    filesystem: new Filesystem(),
                ),
                httpClient: $this->createUnusedHttpClient(),
                eventDispatcher: $this->expectSingleEvent(InfoSheetParsingFailedEvent::class),
                filesystem: new Filesystem(),
            );

            $this->assertSame(
                [],
                $provider->fetchRoundsFromInfoSheet(
                    event: $this->createEventInfo(),
                    infoSheetUrl: 'https://example.test/infosheet',
                ),
            );
        } finally {
            $this->restoreEnv('OPENAI_API_KEY', $previousApiKey);
            $this->restoreEnv('OPENAI_MODEL', $previousModel);
        }
    }

    #[Test]
    public function download_exception_emits_download_failed_event(): void
    {
        $provider = new InfoSheetRoundProvider(
            scheduleProvider: new InfoSheetChatGptScheduleParser(
                httpClient: new Client(),
                scheduleFactory: $this->createScheduleFactory(),
                eventDispatcher: $this->createSilentEventDispatcher(),
            ),
            downloader: new InfoSheetDownloader(
                httpClient: $this->createDownloadFailureHttpClient(),
                filesystem: new Filesystem(),
            ),
            httpClient: $this->createUnusedHttpClient(),
            eventDispatcher: $this->expectSingleEvent(InfoSheetDownloadFailedEvent::class),
            filesystem: new Filesystem(),
        );

        $this->assertSame(
            [],
            $provider->fetchRoundsFromInfoSheet(
                event: $this->createEventInfo(),
                infoSheetUrl: 'https://example.test/infosheet',
            ),
        );
    }

    private function createScheduleFactory(): IFSCScheduleFactory
    {
        return new IFSCScheduleFactory(
            tagsParser: new IFSCTagsParser(),
            roundNameNormalizer: new IFSCRoundNameNormalizer(),
        );
    }

    private function createEventInfo(): IFSCEventInfo
    {
        return new IFSCEventInfo(
            eventId: 42,
            eventName: 'Test Event',
            leagueId: 1,
            leagueName: 'World Cup',
            leagueSeasonId: 1,
            localStartDate: '2026-04-17',
            localEndDate: '2026-04-19',
            timeZone: new DateTimeZone('Europe/Madrid'),
            location: 'Madrid',
            country: 'Spain',
            disciplines: [],
            categories: [],
        );
    }

    private function createDownloadSuccessHttpClient(): HttpClientInterface
    {
        return new class implements HttpClientInterface {
            public function getRetry(string $url, array $options = []): string
            {
                throw new \LogicException('Not used');
            }

            public function getHeaders(string $url, array $options = []): array
            {
                throw new \LogicException('Not used');
            }

            public function get(string $url, array $options): string
            {
                throw new \LogicException('Not used');
            }

            public function getRedirectLocation(string $url): ?string
            {
                throw new \LogicException('Not used');
            }

            public function downloadFile(string $url, string $saveAs): void
            {
                file_put_contents($saveAs, '%PDF-1.4');
            }
        };
    }

    private function createDownloadFailureHttpClient(): HttpClientInterface
    {
        return new class implements HttpClientInterface {
            public function getRetry(string $url, array $options = []): string
            {
                throw new \LogicException('Not used');
            }

            public function getHeaders(string $url, array $options = []): array
            {
                throw new \LogicException('Not used');
            }

            public function get(string $url, array $options): string
            {
                throw new \LogicException('Not used');
            }

            public function getRedirectLocation(string $url): ?string
            {
                throw new \LogicException('Not used');
            }

            /** @throws HttpException */
            public function downloadFile(string $url, string $saveAs): void
            {
                throw new HttpException('boom');
            }
        };
    }

    private function createUnusedHttpClient(): HttpClientInterface
    {
        return new class implements HttpClientInterface {
            public function getRetry(string $url, array $options = []): string
            {
                throw new \LogicException('Not used');
            }

            public function getHeaders(string $url, array $options = []): array
            {
                throw new \LogicException('Not used');
            }

            public function get(string $url, array $options): string
            {
                throw new \LogicException('Not used');
            }

            public function getRedirectLocation(string $url): ?string
            {
                throw new \LogicException('Not used');
            }

            public function downloadFile(string $url, string $saveAs): void
            {
                throw new \LogicException('Not used');
            }
        };
    }

    private function expectSingleEvent(string $eventClass): EventDispatcherInterface
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(static fn (object $event): bool => $event instanceof $eventClass));

        return $eventDispatcher;
    }

    private function createSilentEventDispatcher(): EventDispatcherInterface
    {
        return $this->createStub(EventDispatcherInterface::class);
    }

    private function setEnv(string $name, ?string $value): string|false
    {
        $previousValue = getenv($name);

        if ($value === null) {
            putenv($name);
            unset($_ENV[$name]);

            return $previousValue;
        }

        putenv("{$name}={$value}");
        $_ENV[$name] = $value;

        return $previousValue;
    }

    private function restoreEnv(string $name, string|false $value): void
    {
        if ($value === false) {
            putenv($name);
            unset($_ENV[$name]);

            return;
        }

        putenv("{$name}={$value}");
        $_ENV[$name] = $value;
    }
}
