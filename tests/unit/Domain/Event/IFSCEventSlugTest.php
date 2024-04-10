<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventSlug;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCEventSlugTest extends TestCase
{
    private IFSCEventSlug $slug;

    protected function setUp(): void
    {
        $this->slug = new IFSCEventSlug();
    }

    #[Test]
    #[DataProvider('expected_slugs')]
    public function season_2024_slugs(string $eventName, string $expectedSlug): void
    {
        $this->assertSame($expectedSlug, $this->slug->create($eventName));
    }

    public static function expected_slugs(): array
    {
        return [
            ['IFSC World Cup Keqiao 2024', 'ifsc-world-cup-keqiao-2024'],
            ['IFSC World Cup Wujiang 2024', 'ifsc-world-cup-wujiang-2024'],
            ['IFSC World Cup Salt Lake City 2024', 'ifsc-world-cup-salt-lake-city-2024'],
            ['IFSC World Cup Innsbruck 2024', 'ifsc-world-cup-innsbruck-2024'],
            ['IFSC World Cup Chamonix 2024', 'ifsc-world-cup-chamonix-2024'],
            ['IFSC World Cup Briançon 2024', 'ifsc-world-cup-briancon-2024'],
            ['IFSC World Cup Koper 2024', 'ifsc-world-cup-koper-2024'],
            ['IFSC World Cup Prague 2024', 'ifsc-world-cup-prague-2024'],
            ['IFSC World Cup Seoul 2024', 'ifsc-world-cup-seoul-2024'],
            ['IFSC Paraclimbing World Cup Salt Lake City 2024', 'ifsc-paraclimbing-world-cup-salt-lake-city-2024'],
            ['IFSC Paraclimbing World Cup Innsbruck 2024', 'ifsc-paraclimbing-world-cup-innsbruck-2024'],
            ['IFSC Paraclimbing World Cup Arco 2024', 'ifsc-paraclimbing-world-cup-arco-2024'],
            ['Olympic Qualifier Series Shanghai 2024', 'olympic-qualifier-series-shanghai-2024'],
            ['Olympic Qualifier Series Budapest 2024', 'olympic-qualifier-series-budapest-2024'],
            ['Olympic Games Paris 2024', 'olympic-games-paris-2024'],
        ];
    }
}
