<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Event\Helpers\Normalizer;

final readonly class IFSCRoundFactory
{
    public function __construct(
        private Normalizer $normalizer,
    ) {
    }

    public function create(
        string $name,
        ?string $streamUrl,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
    ): IFSCRound {
        return new IFSCRound(
            name: $name,
            streamUrl: $this->getStreamUrl($streamUrl),
            startTime: $startTime,
            endTime: $endTime,
        );
    }

    private function getStreamUrl(?string $streamUrl): ?string
    {
        return $this->normalizer->normalizeStreamUrl($streamUrl);
    }
}
