<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Discipline;

enum IFSCDiscipline: string
{
    case BOULDER = 'boulder';
    case LEAD = 'lead';
    case SPEED = 'speed';
    case SPEED_RELAY = 'speed_relay';
    case COMBINED = 'combined';

    public function displayName(): string
    {
        return str_replace('_', ' ', $this->value);
    }

    public function calendarDiscipline(): string
    {
        return $this === self::SPEED_RELAY
            ? self::SPEED->value
            : $this->value;
    }
}
