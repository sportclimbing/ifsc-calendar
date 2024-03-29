<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class IFSCRoundNameNormalizer
{
    public function __construct(
        private IFSCTagsParser $tagsParser,
    ) {
    }

    public function normalize(string $name): string
    {
        $name = trim($name);
        $tags = $this->tagsParser->fromString($name);

        $categories = $tags->getCategories();
        $disciplines = $tags->getDisciplines();
        $kind = $tags->getRoundKind();

        if (!$tags->isPreRound() && $categories && $disciplines && $kind) {
            $roundName = '';

            if (count($categories) === 2) {
                $roundName .= "Men's & Women's";
            } else {
                $roundName .= "{$categories[0]->value}'s";
            }

            if (count($disciplines) > 1) {
                $lastDiscipline = array_pop($disciplines);
                $disciplines = $this->disciplineNames($disciplines);
                $roundName .= " " . implode(', ', $disciplines) . ' & ' . $lastDiscipline->value;
            } else {
                $roundName .= " {$disciplines[0]->value}";
            }

            $roundName .= " {$kind->value}";
            $name = $roundName;
        }

        return $this->upperCaseWords($name);
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
}
