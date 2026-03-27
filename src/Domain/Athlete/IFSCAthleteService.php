<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Athlete;

final readonly class IFSCAthleteService
{
    public function __construct(
        private IFSCAthleteProviderInterface $athleteProvider,
    ) {
    }

    /** @throws IFSCAthleteException */
    public function fetchAthlete(int $athleteId): IFSCAthlete
    {
        return $this->athleteProvider->fetchAthlete($athleteId);
    }
}
