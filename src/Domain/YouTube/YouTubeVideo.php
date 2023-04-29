<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use DateTimeImmutable;

final readonly class YouTubeVideo
{
    public function __construct(
        public string $title,
        public string $description,
        public string $videoId,
        public DateTimeImmutable $publishedAt,
    ) {
    }
}
