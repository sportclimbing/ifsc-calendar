<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Season;

use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;

final readonly class IFSCSeason
{
    /** @var IFSCLeague[] */
    public array $leagues;

    public function __construct(
        public string $name,
        public int $id,
        array $leagues,
    ) {
        $this->leagues = $leagues;
    }
}
