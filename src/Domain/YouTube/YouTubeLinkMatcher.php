<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventTagsRegex as Tag;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class YouTubeLinkMatcher
{
    public function __construct(
        private IFSCTagsParser $tagsParser,
    ) {
    }

    private const string YOUTUBE_BASE_URL = 'https://youtu.be/';

    public function findStreamUrlForRound(IFSCEventInfo $event, string $roundName, YouTubeVideoCollection $videoCollection): LiveStream
    {
        foreach ($videoCollection->getIterator() as $video) {
            /** @var YouTubeVideo $video */
            if ($this->videoTitleMatchesRoundName($video, $roundName, $event)) {
                return new LiveStream(
                    url: self::YOUTUBE_BASE_URL . $video->videoId,
                    scheduledStartTime: $video->scheduledStartTime,
                    duration: $video->duration,
                    restrictedRegions: $video->restrictedRegions,
                );
            }
        }

        return new LiveStream();
    }

    private function videoTitleMatchesRoundName(YouTubeVideo $video, string $roundName, IFSCEventInfo $event): bool
    {
        $videoTitle = mb_strtolower($video->title);
        $roundName = mb_strtolower($roundName);

        if (!$this->videoTitleContainsSameLocationAndSeason($videoTitle, $event)) {
            return false;
        }

        $videoTags = $this->fetchTagsFromTitle($videoTitle);
        $eventTags = $this->fetchTagsFromTitle($roundName);

        if ($this->videoIsHighlights($videoTags) || $this->isParaclimbingEvent($event)) {
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
        return $this->tagsParser->fromString($title)->allTags();
    }

    private function videoTitleContainsSameLocationAndSeason(string $videoTitle, IFSCEventInfo $event): bool
    {
        return
            str_contains($videoTitle, mb_strtolower($event->location)) &&
            str_contains($videoTitle, $this->eventSeason($event));
    }

    /** @param Tag[] $videoTags */
    private function videoIsHighlights(array $videoTags): bool
    {
        return
            $this->hasTag($videoTags, Tag::HIGHLIGHTS) ||
            $this->hasTag($videoTags, Tag::PRESS_CONFERENCE) ||
            $this->hasTag($videoTags, Tag::REVIEW);
    }

    /**
     * @param Tag[] $videoTags
     * @param Tag[] $eventTags
     */
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

    /** @param Tag[] $tags */
    private function hasTag(array $tags, Tag $tag): bool
    {
        return in_array($tag, $tags, strict: true);
    }

    /**
     * @param Tag[] $items
     * @return Tag[]
     */
    private function removeTags(array $items, Tag ...$tags): array
    {
        foreach ($tags as $tag) {
            unset($items[array_search($tag, $items)]);
        }

        return array_values($items);
    }

    private function eventSeason(IFSCEventInfo $event): string
    {
        return (new DateTimeImmutable($event->localStartDate))->format('Y');
    }

    private function isParaclimbingEvent(IFSCEventInfo $event): bool
    {
        $eventTags = $this->fetchTagsFromTitle($event->eventName);

        return $this->hasTag($eventTags, Tag::PARACLIMBING);
    }
}
