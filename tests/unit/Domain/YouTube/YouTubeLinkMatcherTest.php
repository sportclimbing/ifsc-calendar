<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\YouTube;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundCategory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundKind;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundStatus;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLinkMatcher;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideo;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeVideoCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class YouTubeLinkMatcherTest extends TestCase
{
    private readonly YouTubeLinkMatcher $linkMatcher;

    #[Test]
    public function seoul_speed_qualifications_url_is_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Speed Qualifications',
            description: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assetUrlMatchesEvent('https://youtu.be/mC1RhpB4uuQ', $event);
    }

    #[Test]
    public function seoul_speed_finals_url_is_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Speed Finals',
            description: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assetUrlMatchesEvent('https://youtu.be/eIa6VYrfqX8', $event);

    }

    #[Test]
    public function seoul_womens_boulder_qualification_qualifications_url_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Women\'s Boulder Qualification',
            description: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function seoul_mens_boulder_qualification_qualifications_url_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Men\'s Boulder Qualification',
            description: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function seoul_boulder_semi_finals_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Boulder Semi-finals',
            description: 'IFSC World Cup Seoul 2023',
            location: 'Seoul',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function hachioji_boulder_qualifications_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Boulder Qualifications',
            description: 'IFSC World Cup Hachioji 2023',
            location: 'Hachioji',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function hachioji_boulder_semi_finals_is_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Women\'s Boulder Semi-final',
            description: 'IFSC World Cup Hachioji 2023',
            location: 'Hachioji',
        );

        $this->assetUrlMatchesEvent('https://youtu.be/kuE-qhRq7Fk', $event);
    }

    #[Test]
    public function salt_lake_city_speed_qualification_is_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Women\'s Speed Qualification',
            description: 'IFSC World Cup Salt Lake City 2023',
            location: 'Salt Lake City',
        );

        $this->assetUrlMatchesEvent('https://youtu.be/n6YyV2ddbb4', $event);
    }

    #[Test]
    public function chamonix_lead_finals(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Men\'s Lead Final',
            description: 'IFSC World Cup Chamonix 2023',
            location: 'Chamonix',
        );

        $this->assetUrlMatchesEvent('https://youtu.be/ZNgbe8vi2OI', $event);
    }

    private function createVideoCollection(): YouTubeVideoCollection
    {
        $titles = [
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
            ));
        }

        return $videoCollection;
    }

    private function createEventWithNameAndDescription(string $name, string $description, string $location): IFSCEvent
    {
        return new IFSCEvent(
            season: IFSCSeasonYear::SEASON_2023,
            eventId: 1292,
            leagueId: 431,
            leagueName: 'World Cups and World Championships',
            timeZone: '',
            eventName: $description,
            location: $location,
            country: 'JPN',
            poster: 'https://cdn.ifsc-climbing.org/images/Events/2023/230506_Jakarta_WC/230415_Poster_JAK23.jpg',
            siteUrl: '',
            startsAt: '2023-09-23T19:30:00+08:00',
            endsAt: '2023-09-23T21:30:00+08:00',
            disciplines: [],
            rounds: [
                new IFSCRound(
                    name: $name,
                    categories: [IFSCRoundCategory::WOMEN],
                    disciplines: [],
                    kind: IFSCRoundKind::FINAL,
                    streamUrl: new StreamUrl(),
                    startTime: new DateTimeImmutable(),
                    endTime: new DateTimeImmutable(),
                    status: IFSCRoundStatus::CONFIRMED,
                ),
            ],
        );
    }

    /** @throws InvalidURLException */
    private function findStreamUrlForEvent(IFSCEvent $event): ?string
    {
        return $this->linkMatcher->findStreamUrlForRound($event->rounds[0], $event, $this->createVideoCollection())->url;
    }

    private function assetUrlMatchesEvent(string $url, IFSCEvent $event): void
    {
        $this->assertSame($url, $this->findStreamUrlForEvent($event));
    }

    protected function setUp(): void
    {
        $this->linkMatcher = new YouTubeLinkMatcher(
            new IFSCTagsParser(),
        );
    }
}
