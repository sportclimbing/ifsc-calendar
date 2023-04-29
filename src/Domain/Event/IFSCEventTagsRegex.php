<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

enum IFSCEventTagsRegex: string
{
    case WOMENS = 'women.s';
    case MENS = 'men.s';
    case LEAD = 'lead';
    case BOULDER = 'boulder';
    case SPEED = 'speed';
    case QUALIFICATIONS = 'qualifications?';
    case SEMI_FINALS = 'semi[-\s]+finals?';
    case FINALS = 'finals?';
    case HIGHLIGHTS = 'highlights';
}
