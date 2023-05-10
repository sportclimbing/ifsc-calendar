<?php declare(strict_types=1);

use nicoSWD\IfscCalendar\Domain\Calendar\Fixes\SeasonFix2023;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFactory;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SeasonFix2023Test extends TestCase
{
    private SeasonFix2023 $season2023Fix;

    public function setUp(): void
    {
        $this->season2023Fix = new SeasonFix2023(
            $this->mockClientReturningFile("bern_2023.html"),
            new IFSCEventFactory('https://ifsc.stream/#/season/%d/event/%d'),
            new Normalizer(),
        );

        parent::setUp();
    }

    #[Test]
    public function bern_2023_events_are_found(): void
    {
        $events = [];
        $newEvents = $this->season2023Fix->fix($events);

        $this->assertCount(19, $newEvents);

        [$event1, $event2] = $newEvents;

        $this->assertSame('Men\'s Boulder Qualification', $event1->name);
        $this->assertSame('2023-08-01T09:00:00+02:00', $this->formatDate($event1->startTime));

        $this->assertSame('Women\'s Lead Qualification', $event2->name);
        $this->assertSame('2023-08-02T11:00:00+02:00', $this->formatDate($event2->startTime));
    }

    private function mockClientReturningFile(string $fileName): HttpClientInterface
    {
        return new class ($this->htmlFile($fileName)) implements HttpClientInterface {
            public function __construct(
                private readonly string $fileName,
            ) {
            }

            public function get(string $url): string
            {
                return file_get_contents($this->fileName);
            }
        };
    }

    private function htmlFile(string $fileName): string
    {
        return __DIR__ . "/../../../../html/{$fileName}";
    }

    private function formatDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(DateTimeInterface::RFC3339);
    }
}