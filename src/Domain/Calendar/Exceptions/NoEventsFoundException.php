<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\Exceptions;

use Exception;

final class NoEventsFoundException extends Exception
{
    public static function forLeague(int $league): self
    {
        return new self("No events found for league '{$league}'");
    }
}
