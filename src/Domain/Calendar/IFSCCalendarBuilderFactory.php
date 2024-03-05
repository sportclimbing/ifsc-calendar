<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use InvalidArgumentException;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCCalendarBuilderFactory
{
    public function __construct(
        private IFSCCalendarGeneratorInterface $icsCalendarGenerator,
        private IFSCCalendarGeneratorInterface $jsonCalendarGenerator,
    ) {
    }

    /**
     * @param IFSCEvent[] $events
     * @throws InvalidArgumentException
     */
    public function generateForFormat(IFSCCalendarFormat $format, array $events): string
    {
        return $this->getGeneratorForFormat($format)->generateForEvents($events);
    }

    /** @throws InvalidArgumentException */
    private function getGeneratorForFormat(IFSCCalendarFormat $format): IFSCCalendarGeneratorInterface
    {
        return match ($format) {
            IFSCCalendarFormat::FORMAT_ICS => $this->icsCalendarGenerator,
            IFSCCalendarFormat::FORMAT_JSON => $this->jsonCalendarGenerator,
        };
    }
}
