<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Schedule;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCParsedTags;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;

readonly final class IFSCScheduleFactory
{
    public function __construct(
        private IFSCTagsParser $tagsParser,
        private IFSCRoundNameNormalizer $roundNameNormalizer,
    ) {
    }

    public function create(
        string $name,
        DateTimeImmutable $startsAt,
        DateTimeImmutable $endsAt,
    ): IFSCSchedule {
        $tags = $this->tagsParser->fromString($name);

        return new IFSCSchedule(
            name: $this->roundNameNormalizer->normalize($tags, $name),
            startsAt: $startsAt,
            endsAt: $endsAt,
            isPreRound: !$this->isRound($tags),
        );
    }

    private function isRound(IFSCParsedTags $tags): bool
    {
        if (!$tags->getDisciplines() ||
            !$tags->getRoundKind() ||
            $tags->isPreRound()
        ) {
            return false;
        }

        return true;
    }
}
