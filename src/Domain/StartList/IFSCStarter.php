<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\StartList;

final class IFSCStarter
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $country,
        public float $score = 0,
        public ?string $photoUrl = null,
    ) {
    }

    public function equals(self $starter): bool
    {
        return
            $this->firstName === $starter->firstName &&
            $this->lastName === $starter->lastName &&
            $this->country === $starter->country;
    }
}
