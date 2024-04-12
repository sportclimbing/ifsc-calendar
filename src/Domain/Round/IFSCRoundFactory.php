<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use DateTimeInterface;
use nicoSWD\IfscCalendar\Domain\Discipline\IFSCDisciplines;
use nicoSWD\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use nicoSWD\IfscCalendar\Domain\Stream\LiveStream;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCParsedTags;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;
use nicoSWD\IfscCalendar\Domain\YouTube\YouTubeLiveStreamFinder;

final readonly class IFSCRoundFactory
{
    private const int DEFAULT_ROUND_DURATION = 90;

    public function __construct(
        private IFSCTagsParser $tagsParser,
        private YouTubeLiveStreamFinder $liveStreamFinder,
    ) {
    }

    public function create(
        IFSCEventInfo $event,
        string $roundName,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        IFSCRoundStatus $status,
    ): IFSCRound {
        $tags = $this->getTags($roundName);
        $liveStream = $this->findLiveStream($event, $roundName);

        if ($liveStream->scheduledStartTime) {
            $startTime = $this->buildStartTime($liveStream, $event);
            $endTime = $this->buildEndTime($startTime, $liveStream);
            $status = IFSCRoundStatus::CONFIRMED;
        }

        return new IFSCRound(
            name: $roundName,
            categories: $tags->getCategories(),
            disciplines: $this->getDisciplines($tags),
            kind: $tags->getRoundKind(),
            liveStream: $liveStream,
            startTime: $startTime,
            endTime: $endTime,
            status: $status,
        );
    }

    private function getTags(string $string): IFSCParsedTags
    {
        return $this->tagsParser->fromString($string);
    }

    private function getDisciplines(IFSCParsedTags $tags): IFSCDisciplines
    {
        return new IFSCDisciplines($tags->getDisciplines());
    }

    private function buildStartTime(LiveStream $liveStream, IFSCEventInfo $event): DateTimeImmutable
    {
        $schedulesStartTime = $liveStream->scheduledStartTime->format(
            DateTimeInterface::RFC3339,
        );

        return (new DateTimeImmutable($schedulesStartTime))
            ->modify('+5 minutes')
            ->setTimezone($event->timeZone);
    }

    private function findLiveStream(IFSCEventInfo $event, string $roundName): LiveStream
    {
        return $this->liveStreamFinder->findLiveStream($event, $roundName);
    }

    private function buildEndTime(DateTimeImmutable $startTime, LiveStream $liveStream): DateTimeImmutable
    {
        if ($liveStream->duration > 0) {
            $duration = $liveStream->duration;
        } else {
            $duration = self::DEFAULT_ROUND_DURATION;
        }

        return $startTime->modify(
            sprintf('+%d minutes', $duration)
        );
    }
}
