<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;

final readonly class IFSCScrapedEventsResult
{
    /** @param IFSCRound[] $rounds */
    public function __construct(
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
        public ?string $poster,
        public array $rounds,
    ) {
    }
}
