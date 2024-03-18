<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Info;

final readonly class IFSCEventCategory
{
    /** @param IFSCEventRound[] $rounds */
    public function __construct(
        public array $rounds,
    ) {
    }
}
