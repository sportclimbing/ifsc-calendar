<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\YouTube;

use DateTimeImmutable;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Event\IFSCEventTagsRegex as Tag;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class YouTubeMatchScorer
{
    private const array DISCIPLINE_TAGS = [
        Tag::BOULDER,
        Tag::LEAD,
        Tag::SPEED,
        Tag::COMBINED,
    ];

    private const array ROUND_KIND_TAGS = [
        Tag::QUALIFICATION,
        Tag::SEMI_FINAL,
        Tag::FINAL,
    ];

    private const array LOCATION_ALIASES = [
        'salt lake city' => ['slc'],
    ];

    public function __construct(
        private IFSCTagsParser $tagsParser,
        private YouTubeTextNormalizer $textNormalizer,
    ) {
    }

    /**
     * @param Tag[] $roundTags
     */
    public function score(YouTubeVideo $video, array $roundTags, IFSCEventInfo $event): ?int
    {
        $videoTitle = mb_strtolower($video->title);
        $videoTags = $this->fetchTagsFromTitle($videoTitle);
        $isParaclimbingEvent = $this->isParaclimbingEvent($event);

        if (!$this->videoTitleContainsSameLocationAndSeason($videoTitle, $event) ||
            $this->videoIsHighlights($videoTags) ||
            !$this->paraclimbingTagsAreCompatible($videoTags, $isParaclimbingEvent) ||
            !$this->roundKindMatches($roundTags, $videoTags) ||
            !$this->disciplinesMatch($roundTags, $videoTags) ||
            !$this->categoriesAreCompatible($roundTags, $videoTags)
        ) {
            return null;
        }

        return $this->tagsScore($roundTags, $videoTags) +
            $this->timingScore($video, $event) +
            $this->eventNameTokensScore($videoTitle, $event);
    }

    /** @return Tag[] */
    private function fetchTagsFromTitle(string $title): array
    {
        return $this->tagsParser->fromString($title)->allTags();
    }

    private function videoTitleContainsSameLocationAndSeason(string $videoTitle, IFSCEventInfo $event): bool
    {
        $normalizedTitle = $this->textNormalizer->normalize($videoTitle);
        $year = $this->eventSeason($event);
        $containsSeason = (bool) preg_match("~\\b{$year}\\b~", $normalizedTitle);

        if (!$containsSeason) {
            return false;
        }

        foreach ($this->locationAliases($event->location) as $alias) {
            if (str_contains($normalizedTitle, $alias)) {
                return true;
            }
        }

        return
            str_contains(
                str_replace(' ', '', $normalizedTitle),
                str_replace(' ', '', $this->textNormalizer->normalize($event->location)),
            );
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
     * @param Tag[] $roundTags
     * @param Tag[] $videoTags
     */
    private function tagsScore(array $roundTags, array $videoTags): int
    {
        $comparableRoundTags = $roundTags;

        if (!$this->hasAnyTag($videoTags, Tag::MEN, Tag::WOMEN)) {
            $comparableRoundTags = $this->removeTags($comparableRoundTags, Tag::MEN, Tag::WOMEN);
        }

        $score = 0;

        foreach ($comparableRoundTags as $roundTag) {
            $score += $this->hasTag($videoTags, $roundTag) ? 6 : -7;
        }

        foreach ($videoTags as $videoTag) {
            if (!$this->hasTag($comparableRoundTags, $videoTag)) {
                $score -= 2;
            }
        }

        if ($comparableRoundTags === $videoTags) {
            $score += 8;
        }

        return $score;
    }

    /** @param Tag[] $tags */
    private function hasTag(array $tags, Tag $tag): bool
    {
        return in_array($tag, $tags, strict: true);
    }

    /** @param Tag[] $tags */
    private function hasAnyTag(array $tags, Tag ...$needle): bool
    {
        return array_any($needle, fn (Tag $tag): bool => $this->hasTag($tags, $tag));

    }

    /**
     * @param Tag[] $items
     * @return Tag[]
     */
    private function removeTags(array $items, Tag ...$tags): array
    {
        foreach ($tags as $tag) {
            $index = array_search($tag, $items, strict: true);

            if ($index !== false) {
                unset($items[$index]);
            }
        }

        return array_values($items);
    }

    private function eventSeason(IFSCEventInfo $event): string
    {
        return new DateTimeImmutable($event->localStartDate)->format('Y');
    }

    private function isParaclimbingEvent(IFSCEventInfo $event): bool
    {
        $eventTags = $this->fetchTagsFromTitle($event->eventName);

        return $this->hasTag($eventTags, Tag::PARACLIMBING);
    }

    /** @param Tag[] $videoTags */
    private function paraclimbingTagsAreCompatible(array $videoTags, bool $isParaclimbingEvent): bool
    {
        $videoIsParaclimbing = $this->hasTag($videoTags, Tag::PARACLIMBING);

        if ($isParaclimbingEvent) {
            return $videoIsParaclimbing;
        }

        return !$videoIsParaclimbing;
    }

    /**
     * @param Tag[] $roundTags
     * @param Tag[] $videoTags
     */
    private function roundKindMatches(array $roundTags, array $videoTags): bool
    {
        foreach (self::ROUND_KIND_TAGS as $roundKindTag) {
            if ($this->hasTag($roundTags, $roundKindTag) && !$this->hasTag($videoTags, $roundKindTag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Tag[] $roundTags
     * @param Tag[] $videoTags
     */
    private function disciplinesMatch(array $roundTags, array $videoTags): bool
    {
        $requiredDisciplines = [];

        foreach (self::DISCIPLINE_TAGS as $disciplineTag) {
            if ($this->hasTag($roundTags, $disciplineTag)) {
                $requiredDisciplines[] = $disciplineTag;
            }
        }

        if (!$requiredDisciplines) {
            return true;
        }

        return array_any($requiredDisciplines, fn (Tag $disciplineTag): bool => $this->hasTag($videoTags, $disciplineTag));

    }

    /**
     * @param Tag[] $roundTags
     * @param Tag[] $videoTags
     */
    private function categoriesAreCompatible(array $roundTags, array $videoTags): bool
    {
        $roundHasMen = $this->hasTag($roundTags, Tag::MEN);
        $roundHasWomen = $this->hasTag($roundTags, Tag::WOMEN);
        $videoHasMen = $this->hasTag($videoTags, Tag::MEN);
        $videoHasWomen = $this->hasTag($videoTags, Tag::WOMEN);

        if ($roundHasMen && !$roundHasWomen) {
            return !$videoHasWomen || $videoHasMen;
        }

        if ($roundHasWomen && !$roundHasMen) {
            return !$videoHasMen || $videoHasWomen;
        }

        return true;
    }

    private function timingScore(YouTubeVideo $video, IFSCEventInfo $event): int
    {
        $videoDate = $video->scheduledStartTime ?? $video->publishedAt;
        $eventStart = $this->createDate($event->localStartDate);
        $eventEnd = $this->createDate($event->localEndDate);

        if (!$eventStart || !$eventEnd) {
            return 0;
        }

        $startBuffer = $eventStart->modify('-2 days');
        $endBuffer = $eventEnd->modify('+2 days');

        if ($videoDate >= $startBuffer && $videoDate <= $endBuffer) {
            return 5;
        }

        $broaderStart = $eventStart->modify('-14 days');
        $broaderEnd = $eventEnd->modify('+14 days');

        if ($videoDate >= $broaderStart && $videoDate <= $broaderEnd) {
            return 1;
        }

        return -4;
    }

    private function eventNameTokensScore(string $videoTitle, IFSCEventInfo $event): int
    {
        $eventName = $this->textNormalizer->normalize($event->eventName);
        $videoTitle = $this->textNormalizer->normalize($videoTitle);
        $score = 0;

        foreach ($this->eventNameTokens($eventName) as $token) {
            if (str_contains($videoTitle, $token)) {
                $score += 1;
            }
        }

        return min(6, $score);
    }

    /** @return string[] */
    private function eventNameTokens(string $eventName): array
    {
        $tokens = preg_split('~\s+~', $eventName);

        if ($tokens === false) {
            return [];
        }

        $stopWords = [
            'ifsc',
            'world',
            'cup',
            'cups',
            'championship',
            'championships',
            'climbing',
            'and',
            'the',
            'series',
        ];

        $normalized = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) <= 2 || in_array($token, $stopWords, true)) {
                continue;
            }

            $normalized[] = $token;
        }

        return array_values(array_unique($normalized));
    }

    /** @return string[] */
    private function locationAliases(string $location): array
    {
        $normalizedLocation = $this->textNormalizer->normalize($location);
        $aliases = [$normalizedLocation, str_replace(' ', '', $normalizedLocation)];

        foreach (self::LOCATION_ALIASES[$normalizedLocation] ?? [] as $alias) {
            $aliases[] = $this->textNormalizer->normalize($alias);
        }

        return array_values(array_unique($aliases));
    }

    private function createDate(string $date): ?DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($date);
        } catch (\Exception) {
            return null;
        }
    }
}
