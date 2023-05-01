<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use Exception;

final class IFSCEventsScraperException extends Exception
{
    public static function timeParseExceptionForEventWithId(string $time, int $eventId): self
    {
        return new self("Unable to parse time '{$time}' for event with ID '{$eventId}'");
    }
}
