<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\DomainEvent\Event;

use nicoSWD\IfscCalendar\Domain\DomainEvent\Event;
use Override;

final class InfoSheetDownloadFailedEvent extends Event
{
    public function __construct(
        private readonly string $eventName,
        private readonly string $reason,
    ) {
    }

    #[Override]
    public function getMessage(): string
    {
        return sprintf("[!] WARNING: Unable to download info sheet for '%s': %s", $this->eventName, $this->reason);
    }
}
