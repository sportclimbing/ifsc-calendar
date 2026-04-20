<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\YouTube;

use DateTimeImmutable;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Stream\LiveStream;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class YouTubeLinkMatcher
{
    private const int MIN_CONFIDENCE_SCORE = 14;
    private const string YOUTUBE_BASE_URL = 'https://youtu.be/';

    public function __construct(
        private IFSCTagsParser $tagsParser,
        private YouTubeMatchScorer $matchScorer,
    ) {
    }

    public function findStreamUrlForRound(IFSCEventInfo $event, string $roundName, YouTubeVideoCollection $videoCollection): LiveStream
    {
        $roundTags = $this->tagsParser->fromString(mb_strtolower($roundName))->allTags();
        $bestVideo = null;
        $bestScore = PHP_INT_MIN;

        foreach ($videoCollection->getIterator() as $video) {
            /** @var YouTubeVideo $video */
            $score = $this->matchScorer->score($video, $roundTags, $event);

            if ($score === null) {
                continue;
            }

            if ($bestVideo === null || $this->isBetterCandidate($video, $score, $bestVideo, $bestScore)) {
                $bestVideo = $video;
                $bestScore = $score;
            }
        }

        if ($bestVideo === null || $bestScore < self::MIN_CONFIDENCE_SCORE) {
            return new LiveStream();
        }

        return new LiveStream(
            url: self::YOUTUBE_BASE_URL . $bestVideo->videoId,
            scheduledStartTime: $bestVideo->scheduledStartTime,
            duration: $bestVideo->duration,
            restrictedRegions: $bestVideo->restrictedRegions,
        );
    }

    private function isBetterCandidate(
        YouTubeVideo $candidate,
        int $candidateScore,
        YouTubeVideo $currentBest,
        int $currentBestScore,
    ): bool {
        if ($candidateScore > $currentBestScore) {
            return true;
        }

        if ($candidateScore < $currentBestScore) {
            return false;
        }

        if ($candidate->scheduledStartTime && !$currentBest->scheduledStartTime) {
            return true;
        }

        if (!$candidate->scheduledStartTime && $currentBest->scheduledStartTime) {
            return false;
        }

        return $this->referenceDateTime($candidate) > $this->referenceDateTime($currentBest);
    }

    private function referenceDateTime(YouTubeVideo $video): DateTimeImmutable
    {
        return $video->scheduledStartTime ?? $video->publishedAt;
    }
}
