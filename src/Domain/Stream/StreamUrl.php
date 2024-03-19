<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Stream;

final readonly class StreamUrl
{
    public function __construct(
        public ?string $url = null,
        public array $restrictedRegions = [],
    ) {
    }

    public function hasUrl(): bool
    {
        return $this->url !== null;
    }
}
