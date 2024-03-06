<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Discipline;

enum IFSCDiscipline: string
{
    case BOULDER = 'boulder';
    case LEAD = 'lead';
    case SPEED = 'speed';
}
