<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Schedule;

use DateTimeImmutable;
use Iterator;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCSchedule;
use nicoSWD\IfscCalendar\Domain\Schedule\IFSCScheduleProvider;
use nicoSWD\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class PDFScheduleProvider implements IFSCScheduleProvider
{
    private const string REGEX_DAY_SCHEDULE = '~
        # day name
        (Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday),?\s*
        # day
        (\d{1,2}(st|nd|rd|th|ve)?,?\s+
        # month
        (January|February|March|April|May|June|July|August|September|October|November|December)){1,2}[\n\s]*
        # schedule name
        (.+)
        # stop at next day block
        (?=(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)|$)~nsUx';

    public function __construct(
        private HTMLNormalizer $htmlNormalizer,
        private IFSCTagsParser $tagsParser,
        private IFSCRoundNameNormalizer $roundNameNormalizer,
    ) {
    }

    /** @inheritdoc */
    public function parseSchedule(string $html): array
    {
        $schedules = [];

        foreach ($this->daySchedules($html) as $daySchedule) {
            foreach ($this->parseDaySchedule($daySchedule) as $schedule) {
                if ($this->isRound($schedule)) {
                    $schedules[] = $schedule;
                }
            }
        }

        return $schedules;
    }

    private function daySchedules(string $html): array
    {
        $normalize = $this->htmlNormalizer->normalize($html);

        if (preg_match_all(self::REGEX_DAY_SCHEDULE, $normalize,$matches)) {
            return $matches[0];
        }

        return [];
    }

    private function parseDaySchedule(string $schedule): Iterator
    {
        [$dayName, $schedule] = $this->parseDayAndSchedule($schedule);

        $scheduleRegex = '~
            (?<start_time>\d?\d:\d\d|follow(?:ing|ed)\s+by)\n?(?:\s*-\s*
            (?<end_time>\d?\d:\d\d))?\s*\n
            (?<name>[^\r\n]+)\s*\n~xi';

        if (preg_match_all($scheduleRegex, $schedule, $match, flags: PREG_UNMATCHED_AS_NULL)) {
            $lastStart = null;

            foreach (array_keys($match['start_time']) as $key) {
                if ($this->followsLastRound($match['start_time'][$key])) {
                    $startsAt = $lastStart->modify('+1 hour');
                } else {
                    $startsAt = $this->createStartDate($dayName, $match['start_time'][$key]);
                }

                $endsAt = $this->createEndDate($dayName, $match['end_time'][$key] ?? null, $startsAt);
                $lastStart = $startsAt;

                yield new IFSCSchedule(
                    name: $this->roundNameNormalizer->normalize($match['name'][$key]),
                    startsAt: $startsAt,
                    endsAt: $endsAt,
                );
            }
        }
    }

    private function createStartDate(string $day, string $time): DateTimeImmutable
    {
        // Year is missing!!

        $day = preg_replace('~(\d{1,2})(?:st|nd|rd|th|ve)~', '$1', $day);

        return DateTimeImmutable::createFromFormat(
            'l j M H:i',
            sprintf(
                '%s %s',
                trim($day),
                trim($time),
            )
        );
    }

    private function createEndDate(string $dayName, ?string $time, DateTimeImmutable $startsAt): DateTimeImmutable
    {
        if ($time !== null && trim($time) !== '') {
            return $this->createStartDate($dayName, $time);
        }

        return $startsAt->modify('+2 hours');
    }

    private function isRound(IFSCSchedule $schedule): bool
    {
        $tags = $this->tagsParser->fromString($schedule->name);

        if (!$tags->getDisciplines() ||
            !$tags->getRoundKind() ||
            $tags->isPreRound()
        ) {
            return false;
        }

        return true;
    }

    private function parseDayAndSchedule(string $schedule): array
    {
        return explode("\n", $this->normalizeTime($schedule), limit: 2);
    }

    private function normalizeTime(string $schedule): string
    {
        return preg_replace('~(\d\d:\d\d)\s*\n(\d\d:\d\d)\s*~', "\$1 - \$2\n", $schedule);
    }

    private function followsLastRound($haystack): bool
    {
        return str_contains(strtolower($haystack), 'follow');
    }
}
