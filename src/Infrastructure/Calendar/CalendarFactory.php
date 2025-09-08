<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Calendar;

use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Presentation\Component;
use Eluceo\iCal\Presentation\Component\Property;
use Eluceo\iCal\Presentation\Component\Property\Value\TextValue;
use Eluceo\iCal\Presentation\Factory\CalendarFactory as EluceoCalendarFactory;
use Generator;

final class CalendarFactory extends EluceoCalendarFactory
{
    private string $calenderName;

    public function createNamedCalendar(Calendar $calendar, string $calenderName): Component
    {
        $this->calenderName = $calenderName;

        return parent::createCalendar($calendar);
    }

    protected function getProperties(Calendar $calendar): Generator
    {
        yield from parent::getProperties($calendar);

        if ($this->calenderName) {
            yield new Property('X-WR-CALNAME', new TextValue($this->calenderName));
        }
    }
}
