<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

final readonly class IFSCEventFetcher
{
    public function __construct(
        private IFSCEventFetcherInterface $eventFetcher,
    ) {
    }

    /** @return IFSCEvent[] */
    public function fetchEventsForLeagueWithId(int $id): array
    {
        return $this->eventFetcher->fetchEventsForLeagueWithId($id);
    }
}
