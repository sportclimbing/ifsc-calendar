<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;

final readonly class IFSCRoundsScraper
{
    public function __construct(
        private IFSCRoundFactory $roundFactory,
        private IFSCRoundProviderInterface $roundProvider,
    ) {
    }

    /** @throws Exception */
    public function fetchRoundsForEvent(IFSCEventInfo $event): IFSCScrapedEventsResult
    {
        return new IFSCScrapedEventsResult(
            rounds: $this->createRounds($event),
        );
    }

    /** @return IFSCRound[] */
    private function createRounds(IFSCEventInfo $event): array
    {
        $rounds = [];

        foreach ($this->roundProvider->fetchRounds($event) as $round) {
            $rounds[] = $this->roundFactory->create(
                event: $event,
                roundName: $round->name,
                startTime: $round->startsAt,
                endTime: $round->endsAt,
                status: IFSCRoundStatus::PROVISIONAL,
            );
        }

        return $rounds;
    }
}
