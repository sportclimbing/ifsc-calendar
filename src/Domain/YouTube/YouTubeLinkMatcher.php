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

final readonly class YouTubeLinkMatcher
{
    private const YOUTUBE_BASE_URL = 'https://youtu.be/';

    public function findStreamUrlForRound(IFSCRound $round, IFSCEvent $event, YouTubeVideoCollection $videoCollection): ?string
    {
        foreach ($videoCollection->getIterator() as $video) {
            if ($this->videoTitleMatchesRoundName($video, $round, $event)) {
                return self::YOUTUBE_BASE_URL . $video->videoId;
            }
        }

        return null;
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

    /** @return Tag[] */
    private function fetchTagsFromTitle(string $title): array
    {
        $tags = [];

        foreach (Tag::cases() as $eventType) {
            if (preg_match("~\b{$eventType->value}\b~", $title)) {
                $tags[] = $eventType;
            }
        }

        return $tags;
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
        if (!$this->hasTag($videoTags, Tag::MENS) &&
            !$this->hasTag($videoTags, Tag::WOMENS)
        ) {
            $eventTags = $this->removeTags(
                $eventTags,
                Tag::MENS,
                Tag::WOMENS,
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
