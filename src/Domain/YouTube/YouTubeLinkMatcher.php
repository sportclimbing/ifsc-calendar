<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventTagsRegex as Tag;
use nicoSWD\IfscCalendar\Domain\Stream\StreamUrl;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class YouTubeLinkMatcher
{
    public function __construct(
        private IFSCTagsParser $tagsParser,
    ) {
    }

    private const string YOUTUBE_BASE_URL = 'https://youtu.be/';

    public function findStreamUrlForRound(IFSCRound $round, IFSCEvent $event, YouTubeVideoCollection $videoCollection): StreamUrl
    {
        foreach ($videoCollection->getIterator() as $video) {
            /** @var YouTubeVideo $video */
            if ($this->videoTitleMatchesRoundName($video, $round, $event)) {
                return new StreamUrl(
                    url: self::YOUTUBE_BASE_URL . $video->videoId,
                    restrictedRegions: $video->restrictedRegions,
                );
            }
        }

        return new StreamUrl();
    }

    private function videoTitleMatchesRoundName(YouTubeVideo $video, IFSCRound $round, IFSCEvent $event): bool
    {
        $videoTitle = mb_strtolower($video->title);
        $roundName = mb_strtolower($round->name);

        if (!$this->videoTitleContainsSameLocationAndSeason($videoTitle, $event)) {
            return false;
        }

        $videoTags = $this->fetchTagsFromTitle($videoTitle);
        $eventTags = $this->fetchTagsFromTitle($roundName);

        if ($this->videoIsHighlights($videoTags)) {
            return false;
        }

        if ($this->videoIsMensAndWomensCombined($videoTags, $eventTags)) {
            return true;
        }

        return $videoTags === $eventTags;
    }

    private function fetchTagsFromTitle(string $title): array
    {
        return $this->tagsParser->fromString($title)->allTags();
    }

    private function videoTitleContainsSameLocationAndSeason(string $videoTitle, IFSCEvent $event): bool
    {
        return
            str_contains($videoTitle, mb_strtolower($event->location)) &&
            str_contains($videoTitle, (string) $event->season->value);
    }

    private function videoIsHighlights(array $videoTags): bool
    {
        return
            $this->hasTag($videoTags, Tag::HIGHLIGHTS) ||
            $this->hasTag($videoTags, Tag::PRESS_CONFERENCE) ||
            $this->hasTag($videoTags, Tag::REVIEW);
    }

    private function videoIsMensAndWomensCombined(array $videoTags, array $eventTags): bool
    {
        if (!$this->hasTag($videoTags, Tag::MEN) &&
            !$this->hasTag($videoTags, Tag::WOMEN)
        ) {
            $eventTags = $this->removeTags(
                $eventTags,
                Tag::MEN,
                Tag::WOMEN,
            );
        }

        return $videoTags === $eventTags;
    }

    private function hasTag(array $item, Tag $tag): bool
    {
        return in_array($tag, $item, strict: true);
    }

    private function removeTags(array $items, Tag ...$tags): array
    {
        foreach ($tags as $tag) {
            unset($items[array_search($tag, $items)]);
        }

        return array_values($items);
    }
}
