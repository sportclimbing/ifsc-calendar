<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Athlete;

final readonly class IFSCAthleteCupDisciplineRanking
{
    public function __construct(
        public int $rank,
        public string $resultUrl,
        public int $dCatId,
        public int $disciplineKindId,
    ) {
    }
}
