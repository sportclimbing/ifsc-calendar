<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;

final readonly class IFSCRound
{
    /**
     * @param IFSCRoundCategory[] $categories
     * @param IFSCDiscipline[] $disciplines
     */
    public function __construct(
        public string $name,
        public array $categories,
        public array $disciplines,
        public IFSCRoundKind $kind,
        public LiveStream $streamUrl,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public IFSCRoundStatus $status,
    ) {
    }
}
