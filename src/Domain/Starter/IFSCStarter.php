<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Starter;

final readonly class IFSCStarter
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $country,
        public float $score,
        public ?string $photoUrl,
    ) {
    }
}
