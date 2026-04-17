<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\Athlete;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteProviderInterface;
use Override;

final readonly class InstagramEnrichedAthleteProvider implements IFSCAthleteProviderInterface
{
    public function __construct(
        private IFSCAthleteProviderInterface $provider,
        private InstagramHandleOverrides $instagramHandleOverrides,
    ) {
    }

    #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
    {
        $athlete = $this->provider->fetchAthlete($athleteId);
        $handle = $this->instagramHandleOverrides->findHandleForAthlete($athleteId) ?? $athlete->instagram;

        if ($handle === null) {
            return $athlete;
        }

        return $athlete->withInstagram($handle);
    }
}
