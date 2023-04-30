<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\YouTube;

use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEventTagsRegex;

final readonly class YouTubeLinkMatcher
{
    private const YOUTUBE_URL = 'https://youtu.be/';

    // Eg: IFSC - Climbing World Cup (B,S) - Seoul (KOR) 2023
    private const REGEX_LOCATION_AND_SEASON = '~.+\s-\s+(?<location>.+)\s+\([a-z]{3}\)\s+(?<season>20\d{2})$~';

    public function findStreamUrlForEvent(IFSCEvent $event, YouTubeVideoCollection $videoCollection): ?string
    {
        foreach ($videoCollection->getIterator() as $video) {
            if ($this->videoTitleMatchesEvent($video, $event)) {
                return self::YOUTUBE_URL . $video->videoId;
            }
        }

        return null;
    }

    private function videoTitleMatchesEvent(YouTubeVideo $video, IFSCEvent $event): bool
    {
        $videoTitle = strtolower($video->title);
        $eventName = strtolower($event->name);

        if (!$this->videoTitleContainsSameLocationAndSeason($videoTitle, $event)) {
            return false;
        }

        $videoTags = $this->fetchTagsFromTitle($videoTitle);
        $eventTags = $this->fetchTagsFromTitle($eventName);

        if ($this->videoIsHighlights($videoTags)) {
            return false;
        }

        if ($this->videoIsMensAndWomensCombined($videoTags, $eventTags)) {
            return true;
        }

        return $videoTags == $eventTags;
    }

    private function fetchTagsFromTitle(string $title): array
    {
        $tags = [];

        foreach (IFSCEventTagsRegex::cases() as $eventType) {
            if (preg_match("~\b{$eventType->value}\b~", $title)) {
                $tags[] = $eventType;
            }
        }

        return $tags;
    }

    public function getLocationAndSeason(IFSCEvent $event): array
    {
        if (preg_match(self::REGEX_LOCATION_AND_SEASON, strtolower($event->description), $eventDetails)) {
            return [
                $eventDetails['location'],
                $eventDetails['season'],
            ];
        }

        return [];
    }

    private function videoTitleContainsSameLocationAndSeason(string $videoTitle, IFSCEvent $event): bool
    {
        $locationAndSeason = $this->getLocationAndSeason($event);

        if (!$locationAndSeason) {
            return false;
        }

        [$location, $season] = $locationAndSeason;

        return
            str_contains($videoTitle, $location) &&
            str_contains($videoTitle, $season);
    }

    public function videoIsHighlights(array $videoTags): bool
    {
        return $this->hasTag($videoTags, IFSCEventTagsRegex::HIGHLIGHTS);
    }

    private function videoIsMensAndWomensCombined(array $videoTags, array $eventTags): bool
    {
        if (!$this->hasTag($videoTags, IFSCEventTagsRegex::MENS) &&
            !$this->hasTag($videoTags, IFSCEventTagsRegex::WOMENS)
        ) {
            $eventTags = $this->removeTags(
                $eventTags,
                IFSCEventTagsRegex::MENS,
                IFSCEventTagsRegex::WOMENS,
            );
        }

        return $videoTags == $eventTags;
    }

    private function hasTag(array $item, IFSCEventTagsRegex $tag): bool
    {
        return in_array($tag, $item, strict: true);
    }

    private function removeTags(array $items, IFSCEventTagsRegex ...$tags): array
    {
        foreach ($tags as $tag) {
            unset($items[array_search($tag, $items)]);
        }

        return $items;
    }
}
