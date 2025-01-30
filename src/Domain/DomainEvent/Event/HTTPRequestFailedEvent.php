<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\DomainEvent\Event;

use nicoSWD\IfscCalendar\Domain\DomainEvent\Event;
use Override;

final class HTTPRequestFailedEvent extends Event
{
    public function __construct(
        private readonly string $url,
        private readonly int $errorCode,
        private readonly int $retryCount,
    ) {
    }

    #[Override]
    public function getMessage(): string
    {
        return "[!] HTTP request to '{$this->url}' failed with code '{$this->errorCode}'. Retrying #{$this->retryCount}...";
    }
}
