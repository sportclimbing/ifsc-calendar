<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Schedule;

use DateTimeImmutable;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class IFSCScheduleFactory
{
    public function __construct(
        private IFSCTagsParser $tagsParser,
        private IFSCRoundNameNormalizer $roundNameNormalizer,
    ) {
    }

    public function create(
        string $name,
        DateTimeImmutable $startsAt,
        ?DateTimeImmutable $endsAt,
    ): IFSCSchedule {
        $tags = $this->tagsParser->fromString($name);

        return new IFSCSchedule(
            name: $this->roundNameNormalizer->normalize($tags, $name),
            startsAt: $startsAt,
            endsAt: $endsAt,
        );
    }
}
