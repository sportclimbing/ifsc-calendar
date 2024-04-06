<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Stream;

use DateTimeImmutable;

final readonly class LiveStream
{
    /** @param string[] $restrictedRegions */
    public function __construct(
        public ?string $url = null,
        public ?DateTimeImmutable $scheduledStartTime = null,
        public array $restrictedRegions = [],
    ) {
    }
}
