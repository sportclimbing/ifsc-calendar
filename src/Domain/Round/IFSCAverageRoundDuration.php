<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Tags\IFSCParsedTags;

final readonly class IFSCAverageRoundDuration
{
    private const int DEFAULT_ROUND_DURATION = 90;

    // Run `bin/avg-round-duration` to generate an updated list
    private const array AVERAGE_DURATIONS = [
        'boulder_final_men' => 90,
        'boulder_final_women' => 90,
        'boulder_final_women_men' => 105,
        'boulder_lead_final_men' => 135,
        'boulder_lead_final_women' => 150,
        'boulder_lead_final_women_men' => 240,
        'boulder_lead_semi_final_men' => 135,
        'boulder_lead_semi_final_women' => 105,
        'boulder_lead_semi_final_women_men' => 120,
        'boulder_semi_final_men' => 150,
        'boulder_semi_final_women' => 135,
        'boulder_semi_final_women_men' => 135,
        'final_women_men' => 135,
        'lead_final_men' => 90,
        'lead_final_women' => 135,
        'lead_final_women_men' => 90,
        'lead_semi_final_men' => 135,
        'lead_semi_final_women' => 150,
        'lead_semi_final_women_men' => 135,
        'speed_final_men' => 60,
        'speed_final_women' => 60,
        'speed_final_women_men' => 60,
        'speed_qualification_men' => 60,
        'speed_qualification_women_men' => 90,
    ];

    public function __construct(
        private IFSCAverageRoundDurationLookupKey $lookupKey,
    ) {
    }

    public function fromTags(IFSCParsedTags $tags): int
    {
        return self::AVERAGE_DURATIONS[$this->lookupKey($tags)]
            ?? self::DEFAULT_ROUND_DURATION;
    }

    private function lookupKey(IFSCParsedTags $tags): string
    {
        return $this->lookupKey->generate($tags);
    }
}
