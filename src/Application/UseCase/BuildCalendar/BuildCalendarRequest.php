<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar;

use nicoSWD\IfscCalendar\Domain\League\IFSCLeague;

final readonly class BuildCalendarRequest
{
    /** @var IFSCLeague[] */
    public array $leagues;
    public int $season;
    public string $format;

    public function __construct(int $season, array $leagues, string $format)
    {
        $this->season = $season;
        $this->leagues = $leagues;
        $this->format = $format;
    }
}
