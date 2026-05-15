<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\StartList;

use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundCategory;

final class IFSCStarter
{
    public ?IFSCRoundCategory $category = null;

    public function __construct(
        public readonly int $athleteId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $country,
        public float $score = 0,
        public ?string $photoUrl = null,
        public ?string $instagram = null,
    ) {
    }

    public function equals(self $starter): bool
    {
        return $this->athleteId === $starter->athleteId;
    }
}
