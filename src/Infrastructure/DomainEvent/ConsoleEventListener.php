<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\DomainEvent;

use nicoSWD\IfscCalendar\Domain\DomainEvent\Event;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventListenerInterface;
use Override;

final class ConsoleEventListener implements EventListenerInterface
{
    #[Override] public function logMessage(Event $event): void
    {
        echo $event->getMessage(), PHP_EOL;
    }
}
