<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Tags;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEventTagsRegex as Tag;

final readonly class IFSCTagsParser
{
    public function fromString(string $string): IFSCParsedTags
    {
        $tags = [];

        foreach (Tag::cases() as $tag) {
            if (preg_match("~\b{$tag->value}\b~i", $string)) {
                $tags[] = $tag;
            }
        }

        return new IFSCParsedTags($tags);
    }
}
