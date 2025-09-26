<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDisciplines;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;

final class IFSCRound
{
    /** @param IFSCRoundCategory[] $categories */
    public function __construct(
        public readonly string $name,
        public private(set) array $categories,
        public readonly IFSCDisciplines $disciplines,
        public readonly IFSCRoundKind $kind,
        public readonly LiveStream $liveStream,
        public readonly DateTimeImmutable $startTime,
        public readonly DateTimeImmutable $endTime,
        public readonly IFSCRoundStatus $status,
    ) {
    }

    public function makeMensAndWomens(): void
    {
        $this->categories = [
            IFSCRoundCategory::MEN,
            IFSCRoundCategory::WOMEN,
        ];
    }

    public function isStreamable(): bool
    {
        return !$this->kind->isQualification() || $this->liveStream->hasUrl();
    }
}
