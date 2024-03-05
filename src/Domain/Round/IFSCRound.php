<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Stream\IFSCStreamUrl;

final class IFSCRound
{
    public function __construct(
        public readonly string $name,
        public IFSCStreamUrl $streamUrl,
        public readonly DateTimeImmutable $startTime,
        public readonly DateTimeImmutable $endTime,
        public readonly bool $scheduleConfirmed = true,
    ) {
    }
}
