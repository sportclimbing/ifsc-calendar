<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Season;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventInfoProviderInterface;
use nicoSWD\IfscCalendar\Infrastructure\IFSC\IFSCApiClientException;

final readonly class IFSCSeasonProvider
{
    public function __construct(
        private IFSCEventInfoProviderInterface $eventInfoProvider,
    ) {
    }

    /**
     * @return IFSCSeason[]
     * @throws IFSCApiClientException
     */
    public function fetchSeasons(): array
    {
        return $this->eventInfoProvider->fetchSeasons();
    }
}
