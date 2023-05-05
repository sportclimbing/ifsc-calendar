<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Helpers;

final readonly class Normalizer
{
    public function leagueName(string $league): string
    {
        return ucwords(strtolower($league));
    }

    public function normalizeTime(string $time): string
    {
        if (in_array($time, ['TBC', 'TBD'], strict: true)) {
            // We don't know the exact time yet. We'll set it to 8:00 for now
            // as it will automatically update once IFSC sets it
            $time = '8:00';
        }

        return $time;
    }

    public function nonEmptyLines(string $matches): array
    {
        return preg_split("~[\r\n]+~", $matches, flags: PREG_SPLIT_NO_EMPTY);
    }

    public function removeNonAsciiCharacters(string $text): string
    {
        return preg_replace('~[^\w\s\'\r\n:,-\./\?=]+~', ' ', $text);
    }
}
