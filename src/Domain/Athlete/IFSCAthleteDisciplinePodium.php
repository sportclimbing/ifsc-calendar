<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Athlete;

final readonly class IFSCAthleteDisciplinePodium
{
    public function __construct(
        public string $disciplineKind,
        public int $total,
        public int $firstPlace,
        public int $secondPlace,
        public int $thirdPlace,
    ) {
    }
}
