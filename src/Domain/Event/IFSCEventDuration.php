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
        string $timeZone,
        int $season,
    ): self {
        [$hour, $minute] = sscanf($time, '%d:%d');

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone($timeZone));
        $date->setDate($season, $month->value, $day);
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
