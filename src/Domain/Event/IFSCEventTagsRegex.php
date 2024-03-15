<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

enum IFSCEventTagsRegex: string
{
    case WOMEN = '(women|female)';
    case MEN = '(men|male)';
    case LEAD = 'lead';
    case BOULDER = 'boulder(ing)?';
    case SPEED = 'speed';
    case COMBINED = 'combined';
    case PARACLIMBING = 'paraclimbing';
    case QUALIFICATION = 'qualifications?';
    case SEMI_FINAL = 'semi[-\s]*finals?';
    case FINAL = '(?<!semi[-\s])fi?nals?'; // "i" is optional because someone at the IFSC can't spell 🥲
    case HIGHLIGHTS = 'highlights';
    case PRESS_CONFERENCE = 'press';
    case REVIEW = 'review';
}
