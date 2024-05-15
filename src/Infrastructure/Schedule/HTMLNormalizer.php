<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Schedule;

final readonly class HTMLNormalizer
{
    public function normalize(string $html): string
    {
        $html = str_replace(["<br/>", "<b>&#160;</b>"], ["\n", ''], $html);
        $html = preg_replace(['~<([ai])\s?[^>]*>.*?</\\1>~', '~<(?:img|hr)[^>]+>~'], '', $html);
        $html = str_replace(["–", "–"], "-", $html);
        $html = str_replace("&#160;", " ", $html);
        $html = preg_replace("~,\s+20\d\d~", '', $html);
        $html = str_replace("&#150;", "-", $html);
        $html = preg_replace('~(\d)[h\.](\d)~', '$1:$2', $html);
        $html = preg_replace(
            '~(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday),\s*(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{1,2})(st|nd|rd|th|ve)?~',
            '$1 $3 $2',
            $html,
        );
        $html = html_entity_decode($html);
        $offset = $this->cutOffOffset2($html);
        $html = substr($html, $offset);
        $lines = preg_split('~\n~', $html, -1, PREG_SPLIT_NO_EMPTY);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);
        $html = implode("\n", $lines);
        $html = $this->fixOqsShanghai($html);

        return trim(strip_tags($html));
    }

    public function normalizeTime(string $schedule): string
    {
        $schedule = preg_replace('~(\d{2})\.(\d{2})~', '$1:$2', $schedule);

        return preg_replace('~(\d\d:\d\d)\s*\n(\d\d:\d\d)\s*~', "\$1 - \$2\n", $schedule);
    }

    private function cutOffOffset2(string $html): int
    {
        $pos = strpos($html, 'PROGRAMME');

        if ($pos === false) {
            $pos = strpos($html, 'Schedule');
        }

        return $pos ?: 0;
    }

    private function fixOqsShanghai(string $html): string
    {
        // Remove line-break
        $html = preg_replace_callback(
            '~Thursday\s*\n16 May~',
            static fn (array $matches): string => str_replace("\n", ' ', $matches[0]),
            $html
        );

        // Re-order lines
        return preg_replace_callback(
            '~(([^\n]+\n){2})(<b>(?:Thursday|Friday) 1\d May\s*[^\n]+\n)~',
            static fn (array $match): string => $match[3] . $match[1],
            $html,
        );
    }

    private function cutOffOffset(string $html): int
    {
        $pos = strpos($html, 'PROGRAMME');

        if ($pos === false) {
            $pos = strpos($html, 'Schedule');
        }

        return $pos ?: 0;
    }
}
