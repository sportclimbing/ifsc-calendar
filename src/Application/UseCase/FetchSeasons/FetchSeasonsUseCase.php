<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\FetchSeasons;

use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonProvider;

final readonly class FetchSeasonsUseCase
{
    public function __construct(
        private IFSCSeasonProvider $seasonProvider,
    ) {
    }

    public function execute(): FetchSeasonsResponse
    {
        return new FetchSeasonsResponse(
            $this->seasonProvider->fetchSeasons(),
        );
    }
}
