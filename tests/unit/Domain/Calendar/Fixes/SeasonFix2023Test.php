<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\Calendar\Fixes;

use nicoSWD\IfscCalendar\Domain\Calendar\PostProcess\Season2023PostProcessor;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventFactory;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use nicoSWD\IfscCalendar\tests\Helpers\MockHttpClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SeasonFix2023Test extends TestCase
{
    use MockHttpClient;

    private readonly Season2023PostProcessor $season2023Fix;

    #[Test]
    public function bern_2023_events_are_found(): void
    {
        $events = [];
        $newEvents = $this->season2023Fix->process($events);

        $this->assertCount(19, $newEvents);

        [
            $event1,
            $event2,
            $event3,
            $event4,
            $event5,
            $event6,
            $event7,
            $event8,
            $event9,
            $event10,
            $event11,
            $event12,
            $event13,
            $event14,
            $event15,
            $event16,
            $event17,
            $event18,
            $event19,
        ] = $newEvents;

        $this->assertSame('Men\'s Boulder Qualification', $event1->name);
        $this->assertSame('2023-08-01T09:00:00+02:00', $this->formatDate($event1->startTime));

        $this->assertSame('Women\'s Lead Qualification', $event2->name);
        $this->assertSame('2023-08-02T11:00:00+02:00', $this->formatDate($event2->startTime));

        $this->assertSame('Men\'s Lead Qualification', $event3->name);
        $this->assertSame('2023-08-03T08:30:00+02:00', $this->formatDate($event3->startTime));

        $this->assertSame('Women\'s Boulder Qualification', $event4->name);
        $this->assertSame('2023-08-03T15:30:00+02:00', $this->formatDate($event4->startTime));

        $this->assertSame('Men\'s Boulder Semi-final', $event5->name);
        $this->assertSame('2023-08-04T10:00:00+02:00', $this->formatDate($event5->startTime));

        $this->assertSame('Men\'s Boulder Final', $event6->name);
        $this->assertSame('2023-08-04T18:30:00+02:00', $this->formatDate($event6->startTime));

        $this->assertSame('Women\'s Boulder Semi-final', $event7->name);
        $this->assertSame('2023-08-05T10:00:00+02:00', $this->formatDate($event7->startTime));

        $this->assertSame('Women\'s Boulder Final', $event8->name);
        $this->assertSame('2023-08-05T18:30:00+02:00', $this->formatDate($event8->startTime));

        $this->assertSame('Lead Semi-finals', $event9->name);
        $this->assertSame('2023-08-06T10:00:00+02:00', $this->formatDate($event9->startTime));

        $this->assertSame('Lead Finals', $event10->name);
        $this->assertSame('2023-08-06T18:30:00+02:00', $this->formatDate($event10->startTime));

        $this->assertSame('Paraclimbing Qualifications', $event11->name);
        $this->assertSame('2023-08-08T09:00:00+02:00', $this->formatDate($event11->startTime));

        $this->assertSame('Women\'s Boulder & Lead Semi-final', $event12->name);
        $this->assertSame('2023-08-09T09:00:00+02:00', $this->formatDate($event12->startTime));

        $this->assertSame('Men\'s Boulder & Lead Semi-final', $event13->name);
        $this->assertSame('2023-08-09T13:00:00+02:00', $this->formatDate($event13->startTime));

        $this->assertSame('Boulder & Lead Semi-finals', $event14->name);
        $this->assertSame('2023-08-09T20:30:00+02:00', $this->formatDate($event14->startTime));

        $this->assertSame('Speed Qualifications', $event15->name);
        $this->assertSame('2023-08-10T09:00:00+02:00', $this->formatDate($event15->startTime));

        $this->assertSame('Paraclimbing Finals', $event16->name);
        $this->assertSame('2023-08-10T14:00:00+02:00', $this->formatDate($event16->startTime));

        $this->assertSame('Speed Finals', $event17->name);
        $this->assertSame('2023-08-10T20:00:00+02:00', $this->formatDate($event17->startTime));

        $this->assertSame('Women\'s Boulder & Lead Final', $event18->name);
        $this->assertSame('2023-08-11T19:00:00+02:00', $this->formatDate($event18->startTime));

        $this->assertSame('Men\'s Boulder & Lead Final', $event19->name);
        $this->assertSame('2023-08-12T16:00:00+02:00', $this->formatDate($event19->startTime));
    }

    protected function setUp(): void
    {
        $this->season2023Fix = new Season2023PostProcessor(
            $this->mockClientReturningFile('bern_2023.html'),
            new IFSCEventFactory('https://ifsc.stream/#/season/%d/event/%d'),
            new Normalizer(),
            new DOMHelper(),
        );

        parent::setUp();
    }
}
