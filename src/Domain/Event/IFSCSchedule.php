<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Stream\IFSCStreamUrl;

final readonly class IFSCSchedule
{
    private function __construct(
        public string $roundName,
        public IFSCStreamUrl $streamUrl,
        public IFSCEventDuration $duration,
    ) {
    }

    public static function create(
        int $day,
        Month $month,
        string $time,
        DateTimeZone $timeZone,
        IFSCSeasonYear $season,
        string $roundName,
        IFSCStreamUrl $streamUrl,
    ): self  {
        return new self(
            roundName: $roundName,
            streamUrl: $streamUrl,
            duration: IFSCEventDuration::create(
                day: $day,
                month: $month,
                time: $time,
                timeZone: $timeZone,
                season: $season,
            ),
        );
    }
}
