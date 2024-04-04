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
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;

final readonly class IFSCRoundsScraper
{
    public function __construct(
        private IFSCRoundFactory $roundFactory,
        private IFSCRoundProviderInterface $roundProvider,
    ) {
    }

    /** @throws Exception */
    public function fetchRoundsAndPosterForEvent(IFSCEventInfo $event): IFSCScrapedEventsResult
    {
        $rounds = $this->roundProvider->fetchRounds($event);

        return new IFSCScrapedEventsResult(
            posterUrl: null,
            rounds: $this->createRounds($rounds),
        );
    }

    /** @param IFSCSchedule[] $schedules */
    private function createRounds(array $schedules): array
    {
        $rounds = [];

        foreach ($schedules as $schedule) {
            $rounds[] = $this->roundFactory->create(
                name: $schedule->name,
                streamUrl: new StreamUrl(),
                startTime: $schedule->startsAt,
                endTime: $schedule->endsAt,
                status: IFSCRoundStatus::CONFIRMED,
            );
        }

        return $rounds;
    }
}
