<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\Athlete;

final readonly class InstagramHandleOverrides
{
    /** @param array<int, string> $handles */
    public function __construct(
        private array $handles,
    ) {
    }

    public function findHandleForAthlete(int $athleteId): ?string
    {
        return $this->handles[$athleteId] ?? null;
    }
}
