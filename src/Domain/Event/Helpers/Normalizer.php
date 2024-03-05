<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Helpers;

final readonly class Normalizer
{
    public function cupName(string $cupName): string
    {
        $cupName = trim($cupName);
        $cupName = strtolower($cupName);
        $cupName = $this->removeNewLines($cupName);
        $cupName = preg_replace('~\s+-\s+(lead|boulder)\s+round$~', '', $cupName);

        return ucwords($cupName);
    }

    public function normalizeTime(string $time): string
    {
        if (in_array($time, ['TBC', 'TBD'], strict: true)) {
            // We don't know the exact time yet. We'll set it to 8:00 for now
            // as it will automatically update once IFSC sets it
            $time = '8:00';
        } else {
            // Convert 12-hour format to 24-hour
            $time = date('H:i', strtotime($time));
        }

        return $time;
    }

    public function nonEmptyLines(string $matches): array
    {
        return preg_split("~[\r\n]+~", $matches, flags: PREG_SPLIT_NO_EMPTY);
    }

    public function removeNonAsciiCharacters(string $text): string
    {
        // This fixes a parsing issue for season 2022
        // This is fun
        $text = preg_replace('~\n\s{10,}~', ' ', $text);

        return preg_replace('~[^\w\s\r\n\':,-./?=&]+~', ' ', $text);
    }

    public function firstUrl(string $urls): ?string
    {
        return preg_split('~\s+~', $urls, flags: PREG_SPLIT_NO_EMPTY)[0] ?? null;
    }

    public function removeNewLines(string $string): string
    {
        return preg_replace('~[\r\n]+~', ' ', $string);
    }
}
