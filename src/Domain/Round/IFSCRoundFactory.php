<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCParsedTags;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class IFSCRoundFactory
{
    public function __construct(
        private IFSCTagsParser $tagsParser,
    ) {
    }

    public function create(
        string $name,
        StreamUrl $streamUrl,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        IFSCRoundStatus $status,
    ): IFSCRound {
        $tags = $this->getTags($name);

        return new IFSCRound(
            name: $name,
            categories: $tags->getCategories(),
            disciplines: $tags->getDisciplines(),
            kind: $tags->getRoundKind(),
            streamUrl: $streamUrl,
            startTime: $startTime,
            endTime: $endTime,
            status: $status,
        );
    }

    private function getTags(string $string): IFSCParsedTags
    {
        return $this->tagsParser->fromString($string);
    }
}
