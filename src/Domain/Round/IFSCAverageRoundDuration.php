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

    private const array AVERAGE_DURATIONS = [
        'men_boulder_final' => 120,
        'men_boulder_semi_final' => 120,
        'women_boulder_final' => 120,
        'women_boulder_semi_final' => 120,
        'lead_final' => 120,
        'speed_final' => 90,
        'lead_semi_final' => 150,
        'speed_qualification' => 90,
        'women_lead_boulder_semi_final' => 90,
        'men_lead_boulder_final' => 180,
        'women_lead_boulder_final' => 180,
        'men_lead_boulder_semi_final' => 180,
        'lead_boulder_final' => 240,
        'lead_boulder_semi_final' => 120,
        'women_men_boulder_final' => 180,
        'women_men_boulder_semi_final' => 150,
        'women_men_speed_final' => 60,
        'women_men_lead_final' => 120,
        'women_men_lead_semi_final' => 150,
        'paraclimbing_final' => 210,
        'men_speed_qualification' => 60,
        'women_speed_final' => 60,
        'boulder_final' => 210,
        'men_lead_final' => 60,
        'men_lead_semi_final' => 120,
        'women_lead_final' => 120,
        'women_lead_semi_final' => 210,
        'boulder_semi_final' => 120,
        'women_men_lead_boulder_semi_final_final' => 360,
        'combined_final' => 420,
        'lead_combined_qualification' => 90,
        'boulder_combined_qualification' => 120,
        'speed_combined_qualification' => 60,
        'men_combined_final' => 240,
        'women_combined_final' => 240,
        'women_boulder_combined_qualification' => 120,
        'men_lead_combined_qualification' => 90,
        'men_boulder_combined_qualification' => 120,
        'women_lead_combined_qualification' => 120,
        'women_final' => 120,
        'women_men_paraclimbing_final' => 150,
        'paraclimbing' => 120,
        'men_boulder_paraclimbing_final' => 150,
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
        $lookupKey = implode('_',
            array_map(
                static fn (Tag $tag): string => $tag->name,
                $tags,
            )
        );

        return strtolower($lookupKey);
    }
}
