<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Calendar;

use SportClimbing\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCCalendarBuilderFactory
{
    public function __construct(
        private IFSCCalendarGeneratorInterface $icsCalendarGenerator,
        private IFSCCalendarGeneratorInterface $jsonCalendarGenerator,
    ) {
    }

    /** @param IFSCEvent[] $events */
    public function generateForFormat(IFSCCalendarFormat $format, array $events): string
    {
        return $this->getGeneratorForFormat($format)->generateForEvents($events);
    }

    private function getGeneratorForFormat(IFSCCalendarFormat $format): IFSCCalendarGeneratorInterface
    {
        return match ($format) {
            IFSCCalendarFormat::FORMAT_ICS => $this->icsCalendarGenerator,
            IFSCCalendarFormat::FORMAT_JSON => $this->jsonCalendarGenerator,
        };
    }
}
