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

final readonly class IFSCRound
{
    /** @param IFSCRoundCategory[] $categories */
    public function __construct(
        public string $name,
        public array $categories,
        public IFSCDisciplines $disciplines,
        public IFSCRoundKind $kind,
        public LiveStream $liveStream,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public IFSCRoundStatus $status,
    ) {
    }
}
