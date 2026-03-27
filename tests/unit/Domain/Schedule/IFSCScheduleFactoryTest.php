<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Domain\Schedule;

use DateTimeImmutable;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use SportClimbing\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCScheduleFactoryTest extends TestCase
{
    private IFSCScheduleFactory $scheduleFactory;

    protected function setUp(): void
    {
        $this->scheduleFactory = new IFSCScheduleFactory(
            new IFSCTagsParser(),
            new IFSCRoundNameNormalizer(),
        );
    }

    #[Test]
    #[DataProvider('event_names')]
    public function event_names_normalize(string $eventName, string $expectedName): void
    {
        $schedule = $this->scheduleFactory->create(
            name: $eventName,
            startsAt: new DateTimeImmutable(),
            endsAt: new DateTimeImmutable(),
        );

        $this->assertSame($expectedName, $schedule->name);
    }

    public static function event_names(): array
    {
        return [
            ["Women’s Boulder Qualification", "Women's Boulder Qualification"],
            ["Men’s & Women’s Lead Semi Finals", "Men's & Women's Lead Semi-Final"],
            ["Lead Warm Up Zone Open", "Lead Warm Up Zone Open"],
            ["Qualification men & women LEAD", "Men's & Women's Lead Qualification"],
        ];
    }
}
