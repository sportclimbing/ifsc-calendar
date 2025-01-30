<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\tests\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCRoundNormalizerTest extends TestCase
{
    private IFSCRoundNameNormalizer $normalizer;
    private IFSCTagsParser $tagsParser;

    protected function setUp(): void
    {
        $this->normalizer = new IFSCRoundNameNormalizer();
        $this->tagsParser = new IFSCTagsParser();
    }

    #[Test]
    #[DataProvider('event_names')]
    public function event_names_normalize(string $eventName, string $expectedName): void
    {
        $tags = $this->tagsParser->fromString($eventName);

        $this->assertSame($expectedName, $this->normalizer->normalize($tags, $eventName));
    }

    public static function event_names(): array
    {
        return [
            ["Opening of Isolation", "Opening Of Isolation"],
            ["Women’s Boulder Qualification", "Women's Boulder Qualification"],
            ["Men’s Boulder Qualification", "Men's Boulder Qualification"],
            ["Women’s Boulder Semi final", "Women's Boulder Semi-Final"],
            ["Women’s Boulder final", "Women's Boulder Final"],
            ["Men’s Boulder Semi final", "Men's Boulder Semi-Final"],
            ["Men’s Boulder Final", "Men's Boulder Final"],
            ["Lead Warm Up Zone Open", "Lead Warm Up Zone Open"],
            ["Lead Qualification", "Men's & Women's Lead Qualification"],
            ["Speed Warm Up Zone Open", "Speed Warm Up Zone Open"],
            ["Speed Practice", "Speed Practice"],
            ["Speed Qualification", "Men's & Women's Speed Qualification"],
            ["Isolation Opens", "Isolation Opens"],
            ["Isolation Closes", "Isolation Closes"],
            ["Men’s & Women’s Lead Semi Finals", "Men's & Women's Lead Semi-Final"],
            ["Men’s & Women’s Speed - Finals", "Men's & Women's Speed Final"],
            ["Women’s Lead - Final", "Women's Lead Final"],
            ["Men’s Lead - Final", "Men's Lead Final"],
            ["Speed Training", "Speed Training"],
            ["Men’s Boulder qualification", "Men's Boulder Qualification"],
            ["Women’s Boulder isolation zone opens", "Women’s Boulder Isolation Zone Opens"],
            ["Women’s Boulder qualification", "Women's Boulder Qualification"],
            ["Men’s Boulder Semifinals", "Men's Boulder Semi-Final"],
            ["Women’s Speed warm-up", "Women’s Speed Warm-Up"],
            ["Women’s Speed practice", "Women’s Speed Practice"],
            ["Women’s Speed Qualification", "Women's Speed Qualification"],
            ["Men’s Boulder final", "Men's Boulder Final"],
            ["Women’s Speed Final", "Women's Speed Final"],
            ["Women’s Boulder Semifinals", "Women's Boulder Semi-Final"],
            ["Men’s Speed warm-up", "Men’s Speed Warm-Up"],
            ["Men’s Speed practice", "Men’s Speed Practice"],
            ["Men’s Speed Qualification", "Men's Speed Qualification"],
            ["Women’s Boulder final", "Women's Boulder Final"],
            ["Men’s Speed Final", "Men's Speed Final"],
            ["Women’s Boulder Semi-Final", "Women's Boulder Semi-Final"],
            ["Women’s Boulder Final", "Women's Boulder Final"],
            ["Men’s Boulder Semi-Final", "Men's Boulder Semi-Final"],
            ["Men’s Boulder Final", "Men's Boulder Final"],
            ["Warm-Up Lead Qualification opens", "Warm-Up Lead Qualification Opens"],
            ["Men’s & Women’s Lead Qualification", "Men's & Women's Lead Qualification"],
            ["Observation Lead Semi-Final", "Observation Lead Semi-Final"],
            ["Men’s & Women’s Lead Semi-Finals ", "Men's & Women's Lead Semi-Final"],
            ["Presentation & Observation Lead Final", "Presentation & Observation Lead Final"],
            ["Men’s Lead Final", "Men's Lead Final"],
            ["Women’s Lead Final", "Women's Lead Final"],
            ["Registration IFSC World Cup SPEED", "Registration IFSC World Cup SPEED"],
            ["Speed Practice Women then Men", "Speed Practice Women Then Men"],
            ["Speed Qualification Women then Men", "Men's & Women's Speed Qualification"],
            ["Registration IFSC World Cup LEAD", "Registration IFSC World Cup LEAD"],
            ["Technical Meeting LEAD", "Technical Meeting LEAD"],
            ["Qualification men & women LEAD", "Men's & Women's Lead Qualification"],
            ["Speed Finals women and men", "Men's & Women's Speed Final"],
            ["Lead semi-finals isolation Zone opens", "Lead Semi-Finals Isolation Zone Opens"],
            ["Lead semi-finals isolation closes", "Lead Semi-Finals Isolation Closes"],
            ["Men’s and women’s LEAD semi-finals", "Men's & Women's Lead Semi-Final"],
            ["Lead Final isolation zone opens", "Lead Final Isolation Zone Opens"],
            ["Observation both genders at the same time", "Observation Both Genders At The Same Time"],
            ["Lead Women Final", "Women's Lead Final"],
            ["Lead Men Final", "Men's Lead Final"],
            ["Speed Qualification Women then Men", "Men's & Women's Speed Qualification"],
            ["Start qualification men & women", "Start Qualification Men & Women"],
            ["Semi-finals men and women", "Semi-Finals Men And Women"],
            ["Presentation, then men final", "Presentation, Then Men Final"],
            ["Presentation, then women final", "Presentation, Then Women Final"],
            ["Registration IFSC World Cup Lead (competition venue)", "Registration IFSC World Cup Lead (competition Venue)"],
            ["Technical Meeting LEAD (competition venue)", "Technical Meeting LEAD (competition Venue)"],
            ["Women’s and Men’s Lead Qualifications", "Men's & Women's Lead Qualification"],
            ["Women’s and Men’s Lead semi-finals isolation Zone", "Women’s And Men’s Lead Semi-Finals Isolation Zone"],
            ["Observation Women", "Observation Women"],
            ["Observation Men", "Observation Men"],
            ["Women’s and Men’s Lead semi-finals", "Men's & Women's Lead Semi-Final"],
            ["Observation time Men and Women", "Observation Time Men And Women"],
            ["Men’s Lead Final", "Men's Lead Final"],
            ["Men’s Boulder final", "Men's Boulder Final"],
            ["Women’s Boulder Final", "Women's Boulder Final"],
            ["Technical Meeting Paraclimbing", "Technical Meeting Paraclimbing"],
            ["Paraclimbing Finals", "Paraclimbing Finals"],
            ["Warm-Up Paraclimbing Opens", "Warm-Up Paraclimbing Opens"],
            ["Paraclimbing Qualification", "Paraclimbing Qualification"],
            ["Isolation Zone open / close Paraclimbing Finals", "Isolation Zone Open / Close Paraclimbing Finals"],
            ["Paraclimbing Finals", "Paraclimbing Finals"],
        ];
    }
}
