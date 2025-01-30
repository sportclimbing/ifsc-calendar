<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\YouTube;

use DateTimeImmutable;
use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkMatcher;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideo;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideoCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class YouTubeLinkMatcherTest extends TestCase
{
    private readonly YouTubeLinkMatcher $linkMatcher;

    #[Test] public function seoul_speed_qualifications_url_is_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Speed Qualifications',
            eventName: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertSame('https://youtu.be/mC1RhpB4uuQ', $liveStream->url);
    }

    #[Test] public function seoul_speed_finals_url_is_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Speed Finals',
            eventName: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertSame('https://youtu.be/eIa6VYrfqX8', $liveStream->url);
    }

    #[Test] public function seoul_womens_boulder_qualification_qualifications_url_is_not_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Women\'s Boulder Qualification',
            eventName: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertNull($liveStream->url);
    }

    #[Test] public function seoul_mens_boulder_qualification_qualifications_url_is_not_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Men\'s Boulder Qualification',
            eventName: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertNull($liveStream->url);
    }

    #[Test] public function seoul_boulder_semi_finals_is_not_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Boulder Semi-finals',
            eventName: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertNull($liveStream->url);
    }

    #[Test] public function hachioji_boulder_qualifications_is_not_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Boulder Qualifications',
            eventName: 'IFSC World Cup Hachioji 2023',
            location: 'Hachioji',
        );

        $this->assertNull($liveStream->url);
    }

    #[Test] public function hachioji_boulder_semi_finals_is_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Women\'s Boulder Semi-final',
            eventName: 'IFSC World Cup Hachioji 2023',
            location: 'Hachioji',
        );

        $this->assertSame('https://youtu.be/kuE-qhRq7Fk', $liveStream->url);
    }

    #[Test] public function salt_lake_city_speed_qualification_is_found(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Women\'s Speed Qualification',
            eventName: 'IFSC World Cup Salt Lake City 2023',
            location: 'Salt Lake City',
        );

        $this->assertSame('https://youtu.be/n6YyV2ddbb4', $liveStream->url);
    }

    #[Test] public function chamonix_lead_finals(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Men\'s Lead Final',
            eventName: 'IFSC World Cup Chamonix 2023',
            location: 'Chamonix',
        );

        $this->assertSame('https://youtu.be/ZNgbe8vi2OI', $liveStream->url);
    }

    #[Test] public function briancon_speed_qualification(): void
    {
        $liveStream = $this->createEventWithNameAndDescription(
            roundName: 'Men\'s Speed Qualification',
            eventName: 'IFSC World Cup Briancon 2023',
            location: 'Briancon',
        );

        $this->assertSame('https://youtu.be/n6YyV2ddb11', $liveStream->url);
    }

    private function createVideoCollection(): YouTubeVideoCollection
    {
        $titles = [
            "n6YyV2ddb11" => "Men's Speed qualification || Briançon 2023",
            "n6YyV2ddb00" => "Paraclimbing Speed qualification || Salt Lake City 2023",
            "n6YyV2ddbb4" => "Women's Speed qualification || Salt Lake City 2023",
            "emrHdLsJTk5" => "Men's Boulder qualification highlights || Seoul 2023",
            "emrHdLsJTk4" => "IFSC World Cup Seoul 2023 || Men's qualification review",
            "dZVTyhrrfao" => "Women's qualifications highlights || Seoul 2023",
            "eIa6VYrfqX8" => "Speed finals || Seoul 2023",
            "_g-5mDjwO0I" => "It's happened! 🤯A sub-5 at the IFSC World Cup in Seoul for Leonardo Veddriq 🇮🇩 #shorts",
            "mC1RhpB4uuQ" => "Speed qualifications || Seoul 2023",
            "4ZfaojD52K4" => "Boulder finals || Seoul 2023",
            "BR9f8FwSPmg" => "Brooke Raboutou 🇺🇸 || Athlete of the Week",
            "UiJ5rYzEm7Y" => "Mejdi Schalck’s top on M1 turned out to be his golden moment at the end of a hard-fought final!",
            "Zij5481HJl4" => "Men's Boulder final highlights || Hachioji 2023",
            "JX_-Ab7-IPY" => "Men's Boulder final || Hachioji 2023",
            "VqUSLFSNOgM" => "Only one way for Brooke Raboutou 🇺🇸  to secure her first-ever World Cup gold medal: topping W4!",
            "_D1hGBIEdQw" => "Men's Boulder semi-final || Hachioji 2023",
            "Cy6pZWQuGJY" => "Women's Boulder final highlights || Hachioji 2023",
            "eNR77KOXi20" => "Women's Boulder final || Hachioji 2023",
            "kuE-qhRq7Fk" => "Women's Boulder semi-final || Hachioji 2023",
            "MQeQs6K_T5g" => "Qualifications highlights || Hachioji 2023",
            "vT8UFPP3I-g" => "IFSC Routesetter Olga Niemiec presents one of the coolest holds you will see on the wall in Hachioji",
            "ZNgbe8vi2OI" => "Lead finals || Chamonix 2023",
            "iF_1fI21Z_w" => "Lead semi-finals || Chamonix 2023",
            "3zfs3s06yPQ" => "Speed finals || Chamonix 2023",
            "h6EYmiImp5g" => "Speed qualifications || Chamonix 2023",
        ];

        $videoCollection = new YouTubeVideoCollection();

        foreach ($titles as $videoId => $title) {
            $videoCollection->add(new YouTubeVideo(
                title: $title,
                duration: 10,
                videoId: $videoId,
                publishedAt: new DateTimeImmutable(),
                scheduledStartTime: new DateTimeImmutable(),
                restrictedRegions: [],
            ));
        }

        return $videoCollection;
    }

    private function createEventWithNameAndDescription(string $roundName, string $eventName, string $location): LiveStream
    {
        $event = new IFSCEventInfo(
            eventId: 1292,
            eventName: $eventName,
            leagueId: 37,
            leagueName: 'World Cups and World Championships',
            leagueSeasonId: 12,
            localStartDate: '2023-04-10T11:55:00Z',
            localEndDate: '2023-04-10T12:55:00Z',
            timeZone: new DateTimeZone('Europe/Madrid'),
            location: $location,
            country: 'JPN',
            disciplines: [],
            categories: [],
        );

        return $this->linkMatcher->findStreamUrlForRound($event, $roundName, $this->createVideoCollection());
    }

    protected function setUp(): void
    {
        $this->linkMatcher = new YouTubeLinkMatcher(
            new IFSCTagsParser(),
        );
    }
}
