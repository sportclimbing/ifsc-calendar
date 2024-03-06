<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\Event;

use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\DOMHelper;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundFactory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundsScraper;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\tests\Helpers\MockHttpClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCEventsScraperTest extends TestCase
{
    use MockHttpClient;

    #[Test]
    public function well_formatted_hachioji_events_are_found(): void
    {
        $events = $this->fetchEventsFromFile(
            fileName: 'hachioji_2023.html',
            timeZone: 'Asia/Tokyo',
        );

        $this->assertCount(5, $events->rounds);

        [$event1, $event2, $event3, $event4, $event5] = $events->rounds;

        $this->assertSame('https://cdn.ifsc-climbing.org/images/Events/2023/230422_Hachioji_WC/a._Poster_HACH23.jpg', $events->poster);

        $this->assertSame('Boulder Qualifications', $event1->name);
        $this->assertSame('2023-04-21T09:00:00+09:00', $this->formatDate($event1->startTime));
        $this->assertSame('2023-04-21T12:00:00+09:00', $this->formatDate($event1->endTime));
        $this->assertSame('https://youtu.be/MQeQs6K_T5g', $event1->streamUrl->url);

        $this->assertSame('Women\'s Boulder Semi-final', $event2->name);
        $this->assertSame('2023-04-22T11:00:00+09:00', $this->formatDate($event2->startTime));
        $this->assertSame('2023-04-22T14:00:00+09:00', $this->formatDate($event2->endTime));
        $this->assertSame('https://youtu.be/kuE-qhRq7Fk', $event2->streamUrl->url);

        $this->assertSame('Women\'s Boulder Final', $event3->name);
        $this->assertSame('2023-04-22T17:00:00+09:00', $this->formatDate($event3->startTime));
        $this->assertSame('2023-04-22T20:00:00+09:00', $this->formatDate($event3->endTime));
        $this->assertSame('https://youtu.be/eNR77KOXi20', $event3->streamUrl->url);

        $this->assertSame('Men\'s Boulder Semi-final', $event4->name);
        $this->assertSame('2023-04-23T11:00:00+09:00', $this->formatDate($event4->startTime));
        $this->assertSame('2023-04-23T14:00:00+09:00', $this->formatDate($event4->endTime));
        $this->assertSame('https://youtu.be/_D1hGBIEdQw', $event4->streamUrl->url);

        $this->assertSame('Men\'s Boulder Final', $event5->name);
        $this->assertSame('2023-04-23T17:00:00+09:00', $this->formatDate($event5->startTime));
        $this->assertSame('2023-04-23T20:00:00+09:00', $this->formatDate($event5->endTime));
        $this->assertSame('https://youtu.be/JX_-Ab7-IPY', $event5->streamUrl->url);
    }

    #[Test]
    public function malformed_seoul_events_are_found(): void
    {
        $events = $this->fetchEventsFromFile(
            fileName: 'seoul_2023.html',
            timeZone: 'Asia/Seoul',
        );

        $this->assertCount(5, $events->rounds);

        [$event1, $event2, $event3, $event4, $event5] = $events->rounds;

        $this->assertSame('https://cdn.ifsc-climbing.org/images/Events/2023/230428_Seoul_WC/230329_Poster_SEOUL23.jpg', $events->poster);

        $this->assertSame('Speed Qualifications', $event1->name);
        $this->assertSame('2023-04-28T12:15:00+09:00', $this->formatDate($event1->startTime));
        $this->assertSame('2023-04-28T15:15:00+09:00', $this->formatDate($event1->endTime));
        $this->assertNull($event1->streamUrl->url);

        $this->assertSame('Speed Finals', $event2->name);
        $this->assertSame('2023-04-28T20:00:00+09:00', $this->formatDate($event2->startTime));
        $this->assertSame('2023-04-28T23:00:00+09:00', $this->formatDate($event2->endTime));
        $this->assertSame('https://youtu.be/eIa6VYrfqX8', $event2->streamUrl->url);

        $this->assertSame('Women\'s Boulder Qualification', $event3->name);
        $this->assertSame('2023-04-29T15:30:00+09:00', $this->formatDate($event3->startTime));
        $this->assertSame('2023-04-29T18:30:00+09:00', $this->formatDate($event3->endTime));
        $this->assertSame('https://youtu.be/dZVTyhrrfao', $event3->streamUrl->url);

        $this->assertSame('Men\'s Boulder Qualification', $event4->name);
        $this->assertSame('2023-04-30T10:00:00+09:00', $this->formatDate($event4->startTime));
        $this->assertSame('2023-04-30T13:00:00+09:00', $this->formatDate($event4->endTime));
        $this->assertSame('https://youtu.be/emrHdLsJTk4', $event4->streamUrl->url);

        $this->assertSame('Boulder Semi-finals', $event5->name);
        $this->assertSame('2023-04-30T18:00:00+09:00', $this->formatDate($event5->startTime));
        $this->assertSame('2023-04-30T21:00:00+09:00', $this->formatDate($event5->endTime));
        $this->assertSame('https://youtu.be/4ZfaojD52K4', $event5->streamUrl->url);
    }

    #[Test]
    public function well_formatted_jakata_events_are_found(): void
    {
        $events = $this->fetchEventsFromFile(
            fileName: 'jakata_2023.html',
            timeZone: 'Asia/Jakarta',
        );

        $this->assertCount(2, $events->rounds);

        [$event1, $event2] = $events->rounds;

        $this->assertSame(null, $events->poster);

        $this->assertSame('Speed Qualifications', $event1->name);
        $this->assertSame('2023-05-06T08:00:00+07:00', $this->formatDate($event1->startTime));
        $this->assertSame('2023-05-06T11:00:00+07:00', $this->formatDate($event1->endTime));
        $this->assertNull($event1->streamUrl->url);

        $this->assertSame('Speed Finals', $event2->name);
        $this->assertSame('2023-05-07T20:00:00+07:00', $this->formatDate($event2->startTime));
        $this->assertSame('2023-05-07T23:00:00+07:00', $this->formatDate($event2->endTime));
        $this->assertNull($event2->streamUrl->url);
    }

    #[Test]
    public function malformed_jakata_events_are_found(): void
    {
        $events = $this->fetchEventsFromFile(
            fileName: 'jakata_2023_malformed.html',
            timeZone: 'Asia/Jakarta',
        );

        $this->assertCount(2, $events->rounds);

        [$event1, $event2] = $events->rounds;

        $this->assertSame('https://cdn.ifsc-climbing.org/images/Events/2023/230506_Jakarta_WC/230415_Poster_JAK23v2.jpg', $events->poster);

        $this->assertSame('Speed Qualifications', $event1->name);
        $this->assertSame('2023-05-06T18:15:00+07:00', $this->formatDate($event1->startTime));
        $this->assertSame('2023-05-06T21:15:00+07:00', $this->formatDate($event1->endTime));
        $this->assertNull($event1->streamUrl->url);

        $this->assertSame('Speed Finals', $event2->name);
        $this->assertSame('2023-05-07T20:00:00+07:00', $this->formatDate($event2->startTime));
        $this->assertSame('2023-05-07T23:00:00+07:00', $this->formatDate($event2->endTime));
        $this->assertNull($event2->streamUrl->url);
    }

    #[Test]
    public function well_formatted_salt_lake_city_events_are_found(): void
    {
        $events = $this->fetchEventsFromFile(
            fileName: 'salt_lake_city_2023.html',
            timeZone: 'America/Denver',
        );

        $this->assertCount(10, $events->rounds);

        [$event1, $event2, $event3, $event4, $event5, $event6, $event7, $event8, $event9, $event10] = $events->rounds;

        $this->assertSame(null, $events->poster);

        $this->assertSame('Women\'s Boulder Qualification', $event1->name);
        $this->assertSame('2023-05-19T09:00:00-06:00', $this->formatDate($event1->startTime));
        $this->assertSame('2023-05-19T12:00:00-06:00', $this->formatDate($event1->endTime));
        $this->assertNull($event1->streamUrl->url);

        $this->assertSame('Men\'s Boulder Qualification', $event2->name);
        $this->assertSame('2023-05-19T15:30:00-06:00', $this->formatDate($event2->startTime));
        $this->assertSame('2023-05-19T18:30:00-06:00', $this->formatDate($event2->endTime));
        $this->assertNull($event2->streamUrl->url);

        $this->assertSame('Women\'s Boulder Semi-final', $event3->name);
        $this->assertSame('2023-05-20T10:00:00-06:00', $this->formatDate($event3->startTime));
        $this->assertSame('2023-05-20T13:00:00-06:00', $this->formatDate($event3->endTime));
        $this->assertNull($event3->streamUrl->url);

        $this->assertSame('Women\'s Speed Qualification', $event4->name);
        $this->assertSame('2023-05-20T15:45:00-06:00', $this->formatDate($event4->startTime));
        $this->assertSame('2023-05-20T18:45:00-06:00', $this->formatDate($event4->endTime));
        $this->assertNull($event4->streamUrl->url);

        $this->assertSame('Women\'s Boulder Final', $event5->name);
        $this->assertSame('2023-05-20T18:30:00-06:00', $this->formatDate($event5->startTime));
        $this->assertSame('2023-05-20T21:30:00-06:00', $this->formatDate($event5->endTime));
        $this->assertNull($event5->streamUrl->url);

        $this->assertSame('Women\'s Speed Final', $event6->name);
        $this->assertSame('2023-05-20T20:30:00-06:00', $this->formatDate($event6->startTime));
        $this->assertSame('2023-05-20T23:30:00-06:00', $this->formatDate($event6->endTime));
        $this->assertNull($event6->streamUrl->url);

        $this->assertSame('Men\'s Boulder Semi-final', $event7->name);
        $this->assertSame('2023-05-21T10:00:00-06:00', $this->formatDate($event7->startTime));
        $this->assertSame('2023-05-21T13:00:00-06:00', $this->formatDate($event7->endTime));
        $this->assertNull($event7->streamUrl->url);

        $this->assertSame('Men\'s Speed Qualification', $event8->name);
        $this->assertSame('2023-05-21T15:45:00-06:00', $this->formatDate($event8->startTime));
        $this->assertSame('2023-05-21T18:45:00-06:00', $this->formatDate($event8->endTime));
        $this->assertNull($event8->streamUrl->url);

        $this->assertSame('Men\'s Boulder Final', $event9->name);
        $this->assertSame('2023-05-21T18:30:00-06:00', $this->formatDate($event9->startTime));
        $this->assertSame('2023-05-21T21:30:00-06:00', $this->formatDate($event9->endTime));
        $this->assertNull($event9->streamUrl->url);

        $this->assertSame('Men\'s Speed Final', $event10->name);
        $this->assertSame('2023-05-21T20:30:00-06:00', $this->formatDate($event10->startTime));
        $this->assertSame('2023-05-21T23:30:00-06:00', $this->formatDate($event10->endTime));
        $this->assertNull($event10->streamUrl->url);
    }

    #[Test]
    public function well_formatted_meiringen_2022_events_are_found(): void
    {
        $events = $this->fetchEventsFromFile(
            fileName: 'meiringen_2022.html',
            timeZone: 'Europe/Zurich',
        );

        $this->assertCount(6, $events->rounds);

        [$event1, $event2, $event3, $event4, $event5, $event6] = $events->rounds;

        $this->assertSame('https://cdn.ifsc-climbing.org/images/Events/2022/220408_Meiringen_WC/IFSC_WC_MEIRINGEN22_poster.jpg', $events->poster);

        $this->assertSame('Women\'s Boulder Qualification', $event1->name);
        $this->assertSame('2023-04-08T09:00:00+02:00', $this->formatDate($event1->startTime));
        $this->assertSame('2023-04-08T12:00:00+02:00', $this->formatDate($event1->endTime));
        $this->assertNull($event1->streamUrl->url);

        $this->assertSame('Men\'s Boulder Qualification', $event2->name);
        $this->assertSame('2023-04-08T16:30:00+02:00', $this->formatDate($event2->startTime));
        $this->assertSame('2023-04-08T19:30:00+02:00', $this->formatDate($event2->endTime));
        $this->assertNull($event2->streamUrl->url);

        $this->assertSame('Women\'s Boulder Semi-final', $event3->name);
        $this->assertSame('2023-04-09T11:00:00+02:00', $this->formatDate($event3->startTime));
        $this->assertSame('2023-04-09T14:00:00+02:00', $this->formatDate($event3->endTime));
        $this->assertSame('https://youtu.be/RMAN27jXQ2k', $event3->streamUrl->url);

        $this->assertSame('Women\'s Boulder Final', $event4->name);
        $this->assertSame('2023-04-09T18:00:00+02:00', $this->formatDate($event4->startTime));
        $this->assertSame('2023-04-09T21:00:00+02:00', $this->formatDate($event4->endTime));
        $this->assertSame('https://youtu.be/44WuwVhkg70', $event4->streamUrl->url);

        $this->assertSame('Men\'s Boulder Semi-final', $event5->name);
        $this->assertSame('2023-04-10T11:00:00+02:00', $this->formatDate($event5->startTime));
        $this->assertSame('2023-04-10T14:00:00+02:00', $this->formatDate($event5->endTime));
        $this->assertSame('https://youtu.be/glsqr3jWTjU', $event5->streamUrl->url);

        $this->assertSame('Men\'s Boulder Final', $event6->name);
        $this->assertSame('2023-04-10T16:00:00+02:00', $this->formatDate($event6->startTime));
        $this->assertSame('2023-04-10T19:00:00+02:00', $this->formatDate($event6->endTime));
        $this->assertSame('https://youtu.be/HPNpg-pLZOg', $event6->streamUrl->url);
    }

    private function fetchEventsFromFile(string $fileName, string $timeZone): IFSCScrapedEventsResult
    {
        $eventScraper = new IFSCRoundsScraper(
            $this->mockClientReturningFile($fileName),
            new IFSCRoundFactory(new IFSCTagsParser()),
            new DOMHelper(),
            new Normalizer(),
        );

        return $eventScraper->fetchRoundsAndPosterForEvent(
            season: IFSCSeasonYear::SEASON_2023,
            eventId: 1249,
            timeZone: new DateTimeZone($timeZone),
        );
    }
}
