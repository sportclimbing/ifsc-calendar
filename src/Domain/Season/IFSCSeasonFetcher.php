<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Season;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventInfoProviderInterface;

final readonly class IFSCSeasonFetcher
{
    public function __construct(
        private IFSCEventInfoProviderInterface $seasonFetcher,
    ) {
    }

    /** @return IFSCSeason[] */
    public function fetchSeasons(): array
    {
        return $this->seasonFetcher->fetchSeasons();
    }
}
