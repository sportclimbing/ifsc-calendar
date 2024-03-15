<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\UseCase\FetchSeasons;

use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonFetcher;

final readonly class FetchSeasonsUseCase
{
    public function __construct(
        private IFSCSeasonFetcher $IFSCSeasonFetcher,
    ) {
    }

    /** @throws HttpException */
    public function execute(): FetchSeasonsResponse
    {
        return new FetchSeasonsResponse(
            $this->IFSCSeasonFetcher->fetchSeasons(),
        );
    }
}
