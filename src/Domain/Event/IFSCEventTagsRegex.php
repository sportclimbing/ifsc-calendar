<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Event;

enum IFSCEventTagsRegex: string
{
    case WOMEN = '(women|female)';
    case MEN = '(men|male)';
    case LEAD = 'lead';
    case BOULDER = 'boulder(ing)?';
    case SPEED = 'speed';
    case COMBINED = 'combined';
    case PARACLIMBING = 'para[\s-]?climbing';
    case QUALIFICATION = 'qualifications?';
    case SEMI_FINAL = 'semi[-\s]*finals?';
    case FINAL = '(?<!semi[-\s])finals?';
    case HIGHLIGHTS = 'highlights';
    case PRESS_CONFERENCE = 'press';
    case REVIEW = 'review';
    case PRE_ROUND = 'warm-?up|observation|practice|isolation|ceremony';
}
