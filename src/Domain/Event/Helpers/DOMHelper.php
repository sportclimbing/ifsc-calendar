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

    private const POSTER_IMAGE_PREFIX = '/images/Events/';

    private const XPATH_SIDEBAR = "//div[@class='text2']";

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

    public function getPoster(DOMXPath $xpath): string
    {
        $sideBar = $xpath->query(self::XPATH_SIDEBAR)->item(0);
        $images = $sideBar?->getElementsByTagName('img') ?? [];

        foreach ($images as $image) {
            foreach ($image->attributes as $name => $attribute) {
                if ($name === 'data-src' && str_starts_with($attribute->textContent, self::POSTER_IMAGE_PREFIX)) {
                    return "https://www.ifsc-climbing.org{$attribute->textContent}";
                }
            }
        }

        return '';
    }

    public function normalizeHtml(string $html): string
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
}
