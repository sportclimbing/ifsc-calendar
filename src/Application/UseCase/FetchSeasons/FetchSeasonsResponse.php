<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\FetchSeasons;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;

final readonly class FetchSeasonsResponse
{
    /** @var IFSCSeason[] */
    public array $seasons;

    public function __construct(array $seasons)
    {
        $this->seasons = $seasons;
    }
}