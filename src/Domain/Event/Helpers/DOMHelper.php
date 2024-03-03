<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Helpers;

use DOMDocument;
use DOMNodeList;
use DOMXPath;

final readonly class DOMHelper
{
    private const XPATH_PARAGRAPHS = "//*[@id='ifsc_event']/div/div/div[@class='text']/p";

    private const XPATH_SIDEBAR = "//div[@class='text2']";

    private const XPATH_EVENT_DATE_RANGE = "//div[@class='title']/h2[@class='date_span']";

    public function htmlToXPath(string $html): DOMXPath
    {
        $lastValue = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($this->normalizeHtml($html));

        libxml_use_internal_errors($lastValue);

        return new DOMXPath($dom);
    }

    public function getParagraphs(DOMXPath $xpath): DOMNodeList
    {
        return $xpath->query(self::XPATH_PARAGRAPHS);
    }

    public function getPoster(DOMXPath $xpath): ?string
    {
        $sideBar = $xpath->query(self::XPATH_SIDEBAR)->item(0);
        $images = $sideBar?->getElementsByTagName('img') ?? [];

        foreach ($images as $image) {
            foreach ($image->attributes as $name => $attribute) {
                if ($name === 'data-src' && $this->hasPosterPrefix($attribute->textContent, $match)) {
                    return "https://cdn.ifsc-climbing.org{$match['path']}";
                }
            }
        }

        return null;
    }

    public function getDateRange(DOMXPath $xpath): string
    {
        return trim($xpath->query(self::XPATH_EVENT_DATE_RANGE)->item(0)->textContent);
    }

    private function normalizeHtml(string $html): string
    {
        $find = [
            // This makes `textContent` display each event in a new line, and thereby easier to parse
            '~<br\s*/?>~i',
            '~</h3>~',
            // This replaces named links with just their blank URL
            '~<a[\s\r\n]+href=\s*(")?([\w:\-./?=]+)\s*[^>]*>(.*?)</a>~si',
        ];

        $replace = [
            "<br/>\n",
            "</h3>\n",
            '$2',
        ];

        return preg_replace($find, $replace, $html);
    }

    private function hasPosterPrefix(string $textContent, ?array &$match): bool
    {
        if (preg_match('~^(?:https://(?:cdn|www)\.ifsc-climbing\.org)?(?<path>/images/Events/[^$]+)~', $textContent, $match) === 1) {
            return true;
        }

        if (str_contains($textContent, 'Events')) {
            $match['path'] = $textContent;
            return true;
        }

        return false;
    }
}
