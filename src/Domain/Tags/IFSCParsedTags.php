<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Tags;

use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventTagsRegex as Tag;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundCategory;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundKind;

final readonly class IFSCParsedTags
{
    private const array DISCIPLINES = [
        IFSCDiscipline::BOULDER->value => Tag::BOULDER,
        IFSCDiscipline::LEAD->value => Tag::LEAD,
        IFSCDiscipline::SPEED->value => Tag::SPEED,
    ];

    private const array ROUND_TYPES = [
        IFSCRoundKind::QUALIFICATION->value => Tag::QUALIFICATION,
        IFSCRoundKind::SEMI_FINAL->value => Tag::SEMI_FINAL,
        IFSCRoundKind::FINAL->value => Tag::FINAL,
    ];

    private const array CATEGORIES = [
        IFSCRoundCategory::WOMEN->value => Tag::WOMEN,
        IFSCRoundCategory::MEN->value => Tag::MEN,
    ];

    /** @param Tag[] $tags */
    public function __construct(
        private array $tags
    ) {
    }

    /** @return Tag[] */
    public function allTags(): array
    {
        return $this->tags;
    }

    /** @return IFSCDiscipline[] */
    public function getDisciplines(): array
    {
        $disciplines = [];

        foreach (self::DISCIPLINES as $name => $tag) {
            if ($this->hasTag($tag)) {
                $disciplines[] = IFSCDiscipline::from($name);
            }
        }

        return $disciplines;
    }

    public function getRoundKind(): ?IFSCRoundKind
    {
        foreach (self::ROUND_TYPES as $name => $tag) {
            if ($this->hasTag($tag)) {
                return IFSCRoundKind::from($name);
            }
        }

        return null;
    }

    public function getCategory(): ?IFSCRoundCategory
    {
        foreach (self::CATEGORIES as $name => $tag) {
            if ($this->hasTag($tag)) {
                return IFSCRoundCategory::from($name);
            }
        }

        return null;
    }

    private function hasTag(Tag $tag): bool
    {
        return in_array($tag, $this->tags, strict: true);
    }
}
