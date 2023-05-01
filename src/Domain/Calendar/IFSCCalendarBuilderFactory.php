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
     * @param string $format
     * @param IFSCEvent[] $events
     * @return string
     * @throws InvalidArgumentException
     */
    public function generateForFormat(string $format, array $events): string
    {
        return $this->getGeneratorForFormat($format)->generateForEvents($events);
    }

    /** @throws InvalidArgumentException */
    private function getGeneratorForFormat(string $format): IFSCCalendarGeneratorInterface
    {
        return match ($format) {
            'ics' => $this->icsCalendarGenerator,
            'json' => $this->jsonCalendarGenerator,
            default => throw new InvalidArgumentException("Unsupported format '{$format}"),
        };
    }
}
