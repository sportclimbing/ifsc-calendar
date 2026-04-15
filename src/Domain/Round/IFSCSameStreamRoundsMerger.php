<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;

final readonly class IFSCSameStreamRoundsMerger
{
    public function __construct(
        private IFSCTagsParser $tagsParser,
        private IFSCRoundNameNormalizer $nameNormalizer,
    ) {
    }

    /**
     * @param IFSCRound[] $rounds
     * @return IFSCRound[]
     */
    public function merge(array $rounds): array
    {
        $groups = $this->groupByStreamUrl($rounds);
        $merged = [];

        foreach ($groups as $group) {
            if (count($group) > 1 && $this->canMerge($group)) {
                $merged[] = $this->buildMergedRound($group);
            } else {
                foreach ($group as $round) {
                    $merged[] = $round;
                }
            }
        }

        usort($merged, static fn(IFSCRound $a, IFSCRound $b) => $a->startTime <=> $b->startTime);

        return $merged;
    }

    /**
     * @param IFSCRound[] $rounds
     * @return IFSCRound[][]
     */
    private function groupByStreamUrl(array $rounds): array
    {
        $groups = [];

        foreach ($rounds as $round) {
            if ($round->liveStream->hasUrl()) {
                $groups[$round->liveStream->url][] = $round;
            } else {
                $groups[] = [$round];
            }
        }

        return $groups;
    }

    /** @param IFSCRound[] $rounds */
    private function canMerge(array $rounds): bool
    {
        $first = $rounds[0];

        foreach (array_slice($rounds, 1) as $round) {
            if ($round->kind !== $first->kind) {
                return false;
            }

            if (!$this->sameDisciplines($round, $first)) {
                return false;
            }
        }

        return true;
    }

    /** @param IFSCRound[] $rounds */
    private function buildMergedRound(array $rounds): IFSCRound
    {
        $first = array_first($rounds);

        return new IFSCRound(
            name: $this->buildMergedName($rounds),
            categories: $this->mergedCategories($rounds),
            disciplines: $first->disciplines,
            kind: $first->kind,
            liveStream: $first->liveStream,
            startTime: $first->startTime,
            endTime: $this->latestEndTime($rounds),
            status: IFSCRoundStatus::CONFIRMED,
        );
    }

    /** @param IFSCRound[] $rounds */
    private function buildMergedName(array $rounds): string
    {
        $combinedName = implode(' ', array_map(static fn (IFSCRound $round) => $round->name, $rounds));
        $tags = $this->tagsParser->fromString($combinedName);

        return $this->nameNormalizer->normalize($tags, $combinedName);
    }

    /**
     * @param IFSCRound[] $rounds
     * @return IFSCRoundCategory[]
     */
    private function mergedCategories(array $rounds): array
    {
        $categories = [];

        foreach ($rounds as $round) {
            foreach ($round->categories as $category) {
                if (!in_array($category, $categories, strict: true)) {
                    $categories[] = $category;
                }
            }
        }

        usort($categories, static fn (IFSCRoundCategory $a, IFSCRoundCategory $b) => $a->value <=> $b->value);

        return $categories;
    }

    /** @param IFSCRound[] $rounds */
    private function latestEndTime(array $rounds): DateTimeImmutable
    {
        return max(array_map(static fn (IFSCRound $round) => $round->endTime, $rounds));
    }

    private function sameDisciplines(IFSCRound $a, IFSCRound $b): bool
    {
        $aValues = array_map(static fn($d) => $d->value, $a->disciplines->all());
        $bValues = array_map(static fn($d) => $d->value, $b->disciplines->all());

        sort($aValues);
        sort($bValues);

        return $aValues === $bValues;
    }
}
