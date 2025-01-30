<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Ranking;

final readonly class IFSCWorldRankCategory
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }
}
