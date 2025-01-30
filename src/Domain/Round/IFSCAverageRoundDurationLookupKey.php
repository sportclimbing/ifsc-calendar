<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Tags\IFSCParsedTags;

final readonly class IFSCAverageRoundDurationLookupKey
{
    public function generate(IFSCParsedTags $tags): string
    {
        $key = [];

        foreach ($tags->getDisciplines() as $tag) {
            $key[] = $tag->name;
        }

        $roundKind = $tags->getRoundKind();

        if ($roundKind) {
            $key[] = $roundKind->name;
        }

        foreach ($tags->getCategories() as $tag) {
            $key[] = $tag->name;
        }

        return strtolower(implode('_', $key));
    }
}
