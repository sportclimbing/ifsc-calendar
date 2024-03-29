<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCParsedTags;

final readonly class IFSCRoundNameNormalizer
{
    public function normalize(IFSCParsedTags $tags, string $originalName): string
    {
        $originalName = trim($originalName);

        $categories = $tags->getCategories();
        $disciplines = $tags->getDisciplines();
        $kind = $tags->getRoundKind();

        if (!$tags->isPreRound() && $disciplines && $kind) {
            $roundName = $this->buildCategories($categories);
            $roundName .= $this->buildDisciplines($disciplines);
            $roundName .= " {$kind->value}";

            $originalName = $roundName;
        }

        return $this->upperCaseWords($originalName);
    }

    private function upperCaseWords(string $name): string
    {
        $callback = static function (array $match): string {
            return $match[1] . ucfirst($match[2]);
        };

        return preg_replace_callback('~(^|-| )([a-z])~', $callback, $name);
    }

    private function disciplineNames(array $disciplines): array
    {
        return array_map(static fn (IFSCDiscipline $discipline): string => $discipline->value, $disciplines);
    }

    private function buildCategories(array $categories): string
    {
        if (count($categories) === 2) {
            return "Men's & Women's";
        } else {
            return "{$categories[0]->value}'s";
        }
    }

    private function buildDisciplines(array $disciplines): string
    {
        if (count($disciplines) > 1) {
            $lastDiscipline = array_pop($disciplines);
            $disciplines = $this->disciplineNames($disciplines);

            return " " . implode(', ', $disciplines) . ' & ' . $lastDiscipline->value;
        } else {
            return " {$disciplines[0]->value}";
        }
    }
}
