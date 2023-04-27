<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;

final readonly class IFSCCalendarBuilderFactory
{
    public function __construct(
        private CalendarGeneratorInterface $icsCalendarGenerator,
        private CalendarGeneratorInterface $jsonCalendarGenerator,
    ) {
    }

    /**
     * @param string $format
     * @param IFSCEvent[] $events
     */
    public function generateForFormat(string $format, array $events): string
    {
        return $this->getGeneratorForFormat($format)->generateForEvents($events);
    }

    /** @throws Exception */
    private function getGeneratorForFormat(string $format): CalendarGeneratorInterface
    {
        return match ($format) {
            'ics' => $this->icsCalendarGenerator,
            'json' => $this->jsonCalendarGenerator,
            default => throw new Exception("Unsupported format '{$format}"),
        };
    }
}
