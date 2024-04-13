<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventTagsRegex as Tag;

final readonly class IFSCAverageRoundDuration
{
    private const int DEFAULT_ROUND_DURATION = 90;

    // Run `bin/avg-round-duration` to generate an updated list
    private const array AVERAGE_DURATIONS = [
        'boulder_combined_men_qualification' => 120,
        'boulder_combined_men_qualification_women' => 120,
        'boulder_combined_qualification_women' => 120,
        'boulder_final_lead_men' => 180,
        'boulder_final_lead_men_semi_final_women' => 360,
        'boulder_final_lead_men_women' => 240,
        'boulder_final_lead_women' => 180,
        'boulder_final_men' => 120,
        'boulder_final_men_paraclimbing' => 150,
        'boulder_final_men_women' => 180,
        'boulder_final_women' => 120,
        'boulder_lead_men_semi_final' => 180,
        'boulder_lead_men_semi_final_women' => 120,
        'boulder_lead_semi_final_women' => 90,
        'boulder_men_semi_final' => 120,
        'boulder_men_semi_final_women' => 120,
        'boulder_semi_final_women' => 120,
        'combined_final_men' => 240,
        'combined_final_men_women' => 420,
        'combined_final_women' => 240,
        'combined_lead_men_qualification' => 90,
        'combined_lead_men_qualification_women' => 90,
        'combined_lead_qualification_women' => 120,
        'combined_men_qualification_speed_women' => 60,
        'final_lead_men' => 60,
        'final_lead_men_women' => 120,
        'final_lead_women' => 120,
        'final_men_paraclimbing_women' => 210,
        'final_men_speed_women' => 90,
        'final_speed_women' => 60,
        'lead_men_semi_final' => 120,
        'lead_men_semi_final_women' => 150,
        'lead_semi_final_women' => 210,
        'men_paraclimbing_women' => 120,
        'men_qualification_speed' => 60,
        'men_qualification_speed_women' => 90,
    ];

    /** @param Tag[] $tags */
    public function fromTags(array $tags): int
    {
        return self::AVERAGE_DURATIONS[$this->lookupKey($tags)]
            ?? self::DEFAULT_ROUND_DURATION;
    }

    /** @param Tag[] $tags */
    private function lookupKey(array $tags): string
    {
        $tags = array_map(
            static fn (Tag $tag): string => $tag->name,
            $tags,
        );

        sort($tags);

        return strtolower(
            implode('_', $tags),
        );
    }
}
