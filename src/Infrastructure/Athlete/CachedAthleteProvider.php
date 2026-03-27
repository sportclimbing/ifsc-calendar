<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\Athlete;

use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthlete;
use SportClimbing\IfscCalendar\Domain\Athlete\IFSCAthleteProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Override;

final readonly class CachedAthleteProvider implements IFSCAthleteProviderInterface
{
    public function __construct(
        private IFSCAthleteProviderInterface $provider,
        private CacheInterface $cache,
        private int $cacheTtlSeconds,
    ) {
    }

    #[Override] public function fetchAthlete(int $athleteId): IFSCAthlete
    {
        return $this->cache->get(
            $this->cacheKey($athleteId),
            function (ItemInterface $item) use ($athleteId): IFSCAthlete {
                $item->expiresAfter($this->cacheTtlSeconds);

                return $this->provider->fetchAthlete($athleteId);
            },
        );
    }

    private function cacheKey(int $athleteId): string
    {
        return sprintf('ifsc.athlete.%d', $athleteId);
    }
}
