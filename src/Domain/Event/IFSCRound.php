<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeImmutable;

final readonly class IFSCRound
{
    public function __construct(
        public string $name,
        public ?string $streamUrl,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public bool $scheduleConfirmed = true,
    ) {
    }

    public function updateStreamUrl(string $streamUrl): self
    {
        return new self(
            name: $this->name,
            streamUrl: $streamUrl,
            startTime: $this->startTime,
            endTime: $this->endTime,
            scheduleConfirmed: $this->scheduleConfirmed,
        );
    }
}
