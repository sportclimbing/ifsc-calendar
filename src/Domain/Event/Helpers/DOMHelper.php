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

    private const POSTER_IMAGE_PREFIX = 'https://cdn.ifsc-climbing.org/images/Events/';

    private const XPATH_SIDEBAR = "//div[@class='text2']";

    public function htmlToDom(string $html): DOMXPath
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

        if (!$sideBar) {
            return '';
        }

        $images = $sideBar->getElementsByTagName('img');

        if (!is_iterable($images)) {
            return '';
        }

        foreach ($images as $image) {
            foreach ($image->attributes as $name => $attribute) {
                if ($name === 'data-src' && str_starts_with($attribute->textContent, self::POSTER_IMAGE_PREFIX)) {
                    return (string) $attribute->textContent;
                }
            }
        }

        return '';
    }

    public function normalizeHtml(string $html): string
    {
        // This makes `textContent` to display each event in a new line, and thereby easier to parse
        $html = preg_replace('~<br\s*/?>~i', "<br/>\n", $html);
        // This replaces named links with just their blank URL
        return preg_replace('~<a[\s\r\n]+href=\s*(")?([\w:\-./\?=]+)\s*[^>]*>(.*?)</a>~s', '$2', $html);
    }
}
