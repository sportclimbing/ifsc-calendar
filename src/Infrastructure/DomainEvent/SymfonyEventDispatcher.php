<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\DomainEvent;

use nicoSWD\IfscCalendar\Domain\DomainEvent\Event;
use nicoSWD\IfscCalendar\Domain\DomainEvent\EventDispatcherInterface;
use Override;
use Symfony\Component\EventDispatcher\EventDispatcher;

final readonly class SymfonyEventDispatcher implements EventDispatcherInterface
{
    private const string EVENT_NAME = 'event.loggable';

    public function __construct(
        private EventDispatcher $eventDispatcher,
    ) {
        $this->eventDispatcher->addListener(self::EVENT_NAME, [new ConsoleEventListener(), 'logMessage']);
    }

    #[Override] public function dispatch(Event $event): void
    {
        $this->eventDispatcher->dispatch($event, self::EVENT_NAME);
    }
}
