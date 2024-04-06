<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use DateTimeImmutable;

readonly final class YouTubeVideo
{
    /** @param string[] $restrictedRegions */
    public function __construct(
        public string $title,
        public int $duration,
        public string $videoId,
        public DateTimeImmutable $publishedAt,
        public ?DateTimeImmutable $scheduledStartTime,
        public array $restrictedRegions,
    ) {
    }
}
