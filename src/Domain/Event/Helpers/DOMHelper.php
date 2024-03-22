<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event\Helpers;

use DOMDocument;
use DOMXPath;

final readonly class DOMHelper
{
    private const string XPATH_EVENT_DATE_RANGE = "//div[contains(@class, 'g:gap-2 uppercase')]";

    public function htmlToXPath(string $html): DOMXPath
    {
        $lastValue = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        libxml_use_internal_errors($lastValue);

        return new DOMXPath($dom);
    }

    public function getDateRange(DOMXPath $xpath): string
    {
        return trim($xpath->query(self::XPATH_EVENT_DATE_RANGE)->item(0)->textContent);
    }
}
