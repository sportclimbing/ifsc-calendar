<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Schedule;

readonly final class HTMLNormalizer
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
        $html = substr($html, strpos($html, 'PROGRAMME'));
        $lines = preg_split('~\n~', $html, -1, PREG_SPLIT_NO_EMPTY);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);
        $html = implode("\n", $lines);

        return strip_tags($html);
    }
}
