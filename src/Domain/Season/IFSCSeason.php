<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Season;

use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;

final readonly class IFSCSeason
{
    /** @var IFSCLeague[] */
    public array $leagues;

    /** @param IFSCLeague[] $leagues */
    public function __construct(
        public string $name,
        array $leagues,
    ) {
        $this->leagues = $leagues;
    }
}
