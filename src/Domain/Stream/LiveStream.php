<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Stream;

use DateTimeImmutable;

final readonly class LiveStream
{
    /** @param string[] $restrictedRegions */
    public function __construct(
        public ?string $url = null,
        public ?DateTimeImmutable $scheduledStartTime = null,
        public int $duration = 0,
        public array $restrictedRegions = [],
    ) {
    }

    public function hasUrl(): bool
    {
        return $this->url !== null;
    }
}
