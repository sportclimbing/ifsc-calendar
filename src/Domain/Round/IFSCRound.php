<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use nicoSWD\IfscCalendar\Domain\Stream\IFSCStreamUrl;

final class IFSCRound
{
    /**
     * @param IFSCRoundCategory[] $categories
     * @param IFSCDiscipline[] $disciplines
     */
    public function __construct(
        public readonly string $name,
        public readonly array $categories,
        public readonly array $disciplines,
        public readonly ?IFSCRoundKind $kind,
        public IFSCStreamUrl $streamUrl,
        public readonly DateTimeImmutable $startTime,
        public readonly DateTimeImmutable $endTime,
        public readonly IFSCRoundStatus $status,
    ) {
    }
}
