<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeZone;
use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCScrapedEventsResult;
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
    public function fetchRoundsAndPosterForEvent(object $event, DateTimeZone $timeZone): IFSCScrapedEventsResult
    {
        $rounds = $this->roundProvider->fetchRounds($event);

        return new IFSCScrapedEventsResult(
            poster: null,
            rounds: $this->createRounds($rounds, $timeZone),
        );
    }

    /** @param IFSCSchedule[] $schedules */
    private function createRounds(array $schedules, DateTimeZone $timeZone): array
    {
        $rounds = [];

        foreach ($schedules as $schedule) {
            $rounds[] = $this->roundFactory->create(
                name: $schedule->name,
                streamUrl: new StreamUrl(),
                startTime: $schedule->startsAt->setTimezone($timeZone),
                endTime: $schedule->endsAt->setTimezone($timeZone),
                status: IFSCRoundStatus::CONFIRMED,
            );
        }

        return $rounds;
    }
}
