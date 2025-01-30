<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\DomainEvent\Event;

use nicoSWD\IfscCalendar\Domain\DomainEvent\Event;
use Override;

final class InfoSheetNotFoundEvent extends Event
{
    public function __construct(
        private readonly string $eventName,
    ) {
    }

    #[Override]
    public function getMessage(): string
    {
        return sprintf("[!] WARNING: No info sheet found for '%s'", $this->eventName);
    }
}
