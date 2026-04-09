<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\StartList;

final readonly class IFSCStartListResult
{
    /** @param IFSCStarter[] $starters */
    public function __construct(
        public array $starters,
        public int $total,
    ) {
    }
}
