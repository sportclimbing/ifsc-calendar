<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeImmutable;

final readonly class IFSCEvent
{
    public function __construct(
        public string $name,
        public int $id,
        public string $description,
        public string $streamUrl,
        public string $siteUrl,
        public string $poster,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
    ) {
    }

    public function updateStreamUrl(string $streamUrl): self
    {
        return new self(
            name: $this->name,
            id: $this->id,
            description: $this->description,
            streamUrl: $streamUrl,
            siteUrl: $this->siteUrl,
            poster: $this->poster,
            startTime: $this->startTime,
            endTime: $this->endTime,
        );
    }
}
