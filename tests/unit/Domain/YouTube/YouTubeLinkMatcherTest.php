<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\YouTube;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
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
            description: 'IFSC - Climbing World Cup (B,S) - Seoul (KOR) 2023',
        );

        $this->assertSame('https://youtu.be/mC1RhpB4uuQ', $this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function seoul_speed_finals_url_is_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Speed Finals',
            description: 'IFSC - Climbing World Cup (B,S) - Seoul (KOR) 2023',
        );

        $this->assertSame('https://youtu.be/eIa6VYrfqX8', $this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function seoul_womens_boulder_qualification_qualifications_url_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Women\'s Boulder Qualification',
            description: 'IFSC - Climbing World Cup (B,S) - Seoul (KOR) 2023',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function seoul_mens_boulder_qualification_qualifications_url_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Men\'s Boulder Qualification',
            description: 'IFSC - Climbing World Cup (B,S) - Seoul (KOR) 2023',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function seoul_boulder_semi_finals_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Boulder Semi-finals',
            description: 'IFSC - Climbing World Cup (B,S) - Seoul (KOR) 2023',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function hachioji_boulder_qualifications_is_not_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Boulder Qualifications',
            description: 'IFSC - Climbing World Cup (B) - Hachioji (JPN) 2023',
        );

        $this->assertNull($this->findStreamUrlForEvent($event));
    }

    #[Test]
    public function hachioji_boulder_semi_finals_is_found(): void
    {
        $event = $this->createEventWithNameAndDescription(
            name: 'Women\'s Boulder Semi-final',
            description: 'IFSC - Climbing World Cup (B) - Hachioji (JPN) 2023',
        );

        $this->assertSame('https://youtu.be/kuE-qhRq7Fk', $this->findStreamUrlForEvent($event));
    }

    private function createVideoCollection(): YouTubeVideoCollection
    {
        $titles = [
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

    private function createEventWithNameAndDescription(string $name, string $description): IFSCEvent
    {
        return new IFSCEvent(
            name: $name,
            id: 1292,
            description: $description,
            streamUrl: '',
            siteUrl: '',
            poster: 'https://cdn.ifsc-climbing.org/images/Events/2023/230506_Jakarta_WC/230415_Poster_JAK23.jpg',
            startTime: new DateTimeImmutable('2023-09-23T19:30:00+08:00'),
            endTime: new DateTimeImmutable('2023-09-23T21:30:00+08:00'),
        );
    }

    private function findStreamUrlForEvent(IFSCEvent $event): ?string
    {
        return $this->linkMatcher->findStreamUrlForEvent($event, $this->createVideoCollection());
    }

    protected function setUp(): void
    {
        $this->linkMatcher = new YouTubeLinkMatcher();
    }
}
