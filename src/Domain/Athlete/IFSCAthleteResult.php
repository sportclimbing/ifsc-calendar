<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Athlete;

final readonly class IFSCAthleteResult
{
    public function __construct(
        public string $season,
        public int $rank,
        public string $discipline,
        public string $eventName,
        public int $eventId,
        public int $dCat,
        public string $date,
        public string $categoryName,
        public string $resultUrl,
    ) {
    }
}
