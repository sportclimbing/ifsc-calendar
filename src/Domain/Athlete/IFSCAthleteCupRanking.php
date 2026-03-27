<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Athlete;

final readonly class IFSCAthleteCupRanking
{
    public function __construct(
        public string $name,
        public int $id,
        public string $season,
        public ?IFSCAthleteCupDisciplineRanking $lead,
        public ?IFSCAthleteCupDisciplineRanking $boulder,
    ) {
    }
}
