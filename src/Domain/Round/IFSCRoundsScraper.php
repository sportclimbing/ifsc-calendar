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
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;

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
        return $this->hydrateRounds(
            event: $event,
            parsedRounds: $this->roundProvider->fetchRounds($event),
        );
    }

    /** @throws Exception */
    public function fetchRoundsFromInfoSheet(IFSCEventInfo $event, string $infoSheetUrl): IFSCScrapedEventsResult
    {
        return $this->hydrateRounds(
            event: $event,
            parsedRounds: $this->roundProvider->fetchRoundsFromInfoSheet($event, $infoSheetUrl)
        );
    }

    /**
     * @param IFSCSchedule[] $parsedRounds
     * @param IFSCEventInfo $event
     * @return IFSCScrapedEventsResult
     */
    public function hydrateRounds(IFSCEventInfo $event, array $parsedRounds): IFSCScrapedEventsResult
    {
        $rounds = [];

        foreach ($parsedRounds as $round) {
            $rounds[] = $this->roundFactory->create(
                event: $event,
                roundName: $round->name,
                startTime: $round->startsAt,
                endTime: $round->endsAt,
                status: IFSCRoundStatus::PROVISIONAL,
            );
        }

        return new IFSCScrapedEventsResult(
            rounds: $rounds,
        );
    }
}
