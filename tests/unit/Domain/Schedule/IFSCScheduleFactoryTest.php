<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\Schedule;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleFactory;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
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
    public function event_names_normalize(string $eventName, bool $isPreRound): void
    {
        $schedule = $this->scheduleFactory->create(
            name: $eventName,
            startsAt: new DateTimeImmutable(),
            endsAt: new DateTimeImmutable(),
        );

        $this->assertSame($schedule->isPreRound, $isPreRound);
    }

    public static function event_names(): array
    {
        return [
            ["Women’s Boulder Qualification", false],
            ["Men’s Boulder Qualification", false],
            ["Women’s Boulder Semi final", false],
            ["Women’s Boulder final", false],
            ["Men’s Boulder Semi final", false],
            ["Men’s Boulder Final", false],
            ["Lead Warm Up Zone Open", true],
            ["Lead Qualification", false],
            ["Speed Warm Up Zone Open", true],
            ["Speed Practice", true],
            ["Speed Qualification", false],
            ["Isolation Opens", true],
            ["Isolation Closes", true],
            ["Men’s & Women’s Lead Semi Finals", false],
            ["Men’s & Women’s Speed - Finals", false],
            ["Women’s Lead - Final", false],
            ["Men’s Lead - Final", false],
            ["Speed Training", true],
            ["Men’s Boulder qualification", false],
            ["Women’s Boulder isolation zone opens", true],
            ["Women’s Boulder qualification", false],
            ["Men’s Boulder Semifinals", false],
            ["Women’s Speed warm-up", true],
            ["Women’s Speed practice", true],
            ["Women’s Speed Qualification", false],
            ["Men’s Boulder final", false],
            ["Women’s Speed Final", false],
            ["Women’s Boulder Semifinals", false],
            ["Men’s Speed warm-up", true],
            ["Men’s Speed practice", true],
            ["Men’s Speed Qualification", false],
            ["Women’s Boulder final", false],
            ["Men’s Speed Final", false],
            ["Women’s Boulder Semi-Final", false],
            ["Women’s Boulder Final", false],
            ["Men’s Boulder Semi-Final", false],
            ["Men’s Boulder Final", false],
            ["Warm-Up Lead Qualification opens", true],
            ["Men’s & Women’s Lead Qualification", false],
            ["Observation Lead Semi-Final", true],
            ["Men’s & Women’s Lead Semi-Finals ", false],
            ["Presentation & Observation Lead Final", true],
            ["Men’s Lead Final", false],
            ["Women’s Lead Final", false],
            ["Registration IFSC World Cup SPEED", true],
            ["Speed Practice Women then Men", true],
            ["Speed Qualification Women then Men", false],
            ["Registration IFSC World Cup LEAD", true],
            ["Technical Meeting LEAD", true],
            ["Qualification men & women LEAD", false],
            ["Speed Finals women and men", false],
            ["Lead semi-finals isolation Zone opens", true],
            ["Lead semi-finals isolation closes", true],
            ["Men’s and women’s LEAD semi-finals", false],
            ["Lead Final isolation zone opens", true],
            ["Observation both genders at the same time", true],
            ["Lead Women Final", false],
            ["Lead Men Final", false],
            ["Speed Qualification Women then Men", false],
            ["Start qualification men & women", true],
            ["Semi-finals men and women", true],
            ["Presentation, then men final", true],
            ["Presentation, then women final", true],
            ["Registration IFSC World Cup Lead (competition venue)", true],
            ["Technical Meeting LEAD (competition venue)", true],
            ["Women’s and Men’s Lead Qualifications", false],
            ["Women’s and Men’s Lead semi-finals isolation Zone", true],
            ["Observation Women", true],
            ["Observation Men", true],
            ["Women’s and Men’s Lead semi-finals", false],
            ["Observation time Men and Women", true],
            ["Men’s Lead Final", false],
            ["Men’s Boulder final", false],
            ["Women’s Boulder Final", false],
            ["Technical Meeting Paraclimbing", true],
            ["Paraclimbing Finals", true],
            ["Warm-Up Paraclimbing Opens", true],
            ["Paraclimbing Qualification", true],
            ["Isolation Zone open / close Paraclimbing Finals", true],
            ["Paraclimbing Finals", true],
        ];
    }
}
