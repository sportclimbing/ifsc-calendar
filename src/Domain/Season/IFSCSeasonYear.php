<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Season;

enum IFSCSeasonYear: int
{
    case SEASON_2020 = 2020;
    case SEASON_2021 = 2021;
    case SEASON_2022 = 2022;
    case SEASON_2023 = 2023;
    case SEASON_2024 = 2024;
    case SEASON_2025 = 2025;
}
