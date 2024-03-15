<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Season;

use nicoSWD\IfscCalendar\Domain\HttpClient\HttpException;

interface IFSCSeasonFetcherInterface
{
    /**
     * @return IFSCSeason[]
     * @throws HttpException
     */
    public function fetchSeasons(): array;

    /** @throws HttpException */
    public function fetchLeagueNameById(IFSCSeasonYear $season, int $leagueId): string;
}
