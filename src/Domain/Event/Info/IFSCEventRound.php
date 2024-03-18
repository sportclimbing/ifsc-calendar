<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Info;

use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundKind;

final readonly class IFSCEventRound
{
    public function __construct(
        public string $discipline,
        public IFSCRoundKind $kind,
        public string $category,
    ) {
    }
}
