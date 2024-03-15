<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

final readonly class IFSCEventDuration
{
    private function __construct(
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
    ) {
    }

    public static function create(
        int $day,
        Month $month,
        string $time,
        DateTimeZone $timeZone,
        IFSCSeasonYear $season,
    ): self {
        [$hour, $minute] = sscanf($time, '%d:%d');

        $date = new DateTime();
        $date->setTimezone($timeZone);
        $date->setDate($season->value, $month->value, $day);
        $date->setTime($hour, $minute);

        $startTime = DateTimeImmutable::createFromMutable($date);

        $endDate = DateTime::createFromImmutable($startTime);
        $endDate->modify('+3 hours');

        return new self(
            $startTime,
            DateTimeImmutable::createFromMutable($endDate),
        );
    }
}
