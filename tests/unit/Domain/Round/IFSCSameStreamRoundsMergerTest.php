<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Domain\Round;

use DateTimeImmutable;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDiscipline;
use SportClimbing\IfscCalendar\Domain\Discipline\IFSCDisciplines;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRound;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundCategory;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundKind;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundNameNormalizer;
use SportClimbing\IfscCalendar\Domain\Round\IFSCRoundStatus;
use SportClimbing\IfscCalendar\Domain\Round\IFSCSameStreamRoundsMerger;
use SportClimbing\IfscCalendar\Domain\Stream\LiveStream;
use SportClimbing\IfscCalendar\Domain\Tags\IFSCTagsParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IFSCSameStreamRoundsMergerTest extends TestCase
{
    private IFSCSameStreamRoundsMerger $merger;

    protected function setUp(): void
    {
        $this->merger = new IFSCSameStreamRoundsMerger(
            new IFSCTagsParser(),
            new IFSCRoundNameNormalizer(),
        );
    }

    #[Test] public function mens_and_womens_rounds_sharing_a_stream_are_merged_into_one(): void
    {
        $stream = new LiveStream(url: 'https://youtu.be/abc123');

        $mens = $this->makeRound("Men's Lead Final", [IFSCRoundCategory::MEN], IFSCRoundKind::FINAL, $stream, '2025-05-10 14:00', '2025-05-10 15:30');
        $womens = $this->makeRound("Women's Lead Final", [IFSCRoundCategory::WOMEN], IFSCRoundKind::FINAL, $stream, '2025-05-10 15:30', '2025-05-10 17:00');

        $result = $this->merger->merge([$mens, $womens]);

        $this->assertCount(1, $result);
        $this->assertSame("Men's & Women's Lead Final", $result[0]->name);
        $this->assertSame([IFSCRoundCategory::MEN, IFSCRoundCategory::WOMEN], $result[0]->categories);
        $this->assertSame($mens->startTime, $result[0]->startTime);
        $this->assertSame('2025-05-10T17:00:00+00:00', $result[0]->endTime->format(DATE_RFC3339));
        $this->assertSame('https://youtu.be/abc123', $result[0]->liveStream->url);
    }

    #[Test] public function merged_round_name_has_men_before_women_regardless_of_input_order(): void
    {
        $stream = new LiveStream(url: 'https://youtu.be/abc123');

        $womens = $this->makeRound("Women's Boulder Qualification", [IFSCRoundCategory::WOMEN], IFSCRoundKind::QUALIFICATION, $stream, '2025-05-10 09:00', '2025-05-10 12:00');
        $mens = $this->makeRound("Men's Boulder Qualification", [IFSCRoundCategory::MEN], IFSCRoundKind::QUALIFICATION, $stream, '2025-05-10 12:00', '2025-05-10 15:00');

        $result = $this->merger->merge([$womens, $mens]);

        $this->assertCount(1, $result);
        $this->assertSame("Men's & Women's Boulder Qualification", $result[0]->name);
        $this->assertSame([IFSCRoundCategory::MEN, IFSCRoundCategory::WOMEN], $result[0]->categories);
    }

    #[Test] public function rounds_with_different_stream_urls_are_not_merged(): void
    {
        $streamA = new LiveStream(url: 'https://youtu.be/aaa');
        $streamB = new LiveStream(url: 'https://youtu.be/bbb');

        $mens = $this->makeRound("Men's Lead Final", [IFSCRoundCategory::MEN], IFSCRoundKind::FINAL, $streamA, '2025-05-10 14:00', '2025-05-10 15:30');
        $womens = $this->makeRound("Women's Lead Final", [IFSCRoundCategory::WOMEN], IFSCRoundKind::FINAL, $streamB, '2025-05-10 15:30', '2025-05-10 17:00');

        $result = $this->merger->merge([$mens, $womens]);

        $this->assertCount(2, $result);
    }

    #[Test] public function rounds_without_a_stream_url_are_not_merged(): void
    {
        $noStream = new LiveStream();

        $mens = $this->makeRound("Men's Lead Final", [IFSCRoundCategory::MEN], IFSCRoundKind::FINAL, $noStream, '2025-05-10 14:00', '2025-05-10 15:30');
        $womens = $this->makeRound("Women's Lead Final", [IFSCRoundCategory::WOMEN], IFSCRoundKind::FINAL, $noStream, '2025-05-10 15:30', '2025-05-10 17:00');

        $result = $this->merger->merge([$mens, $womens]);

        $this->assertCount(2, $result);
    }

    #[Test] public function rounds_sharing_a_stream_but_with_different_kinds_are_not_merged(): void
    {
        $stream = new LiveStream(url: 'https://youtu.be/abc123');

        $qual = $this->makeRound("Men's Lead Qualification", [IFSCRoundCategory::MEN], IFSCRoundKind::QUALIFICATION, $stream, '2025-05-10 09:00', '2025-05-10 12:00');
        $final = $this->makeRound("Women's Lead Final", [IFSCRoundCategory::WOMEN], IFSCRoundKind::FINAL, $stream, '2025-05-10 15:00', '2025-05-10 17:00');

        $result = $this->merger->merge([$qual, $final]);

        $this->assertCount(2, $result);
    }

    #[Test] public function rounds_sharing_a_stream_but_with_different_disciplines_are_not_merged(): void
    {
        $stream = new LiveStream(url: 'https://youtu.be/abc123');

        $lead = $this->makeRound("Men's Lead Final", [IFSCRoundCategory::MEN], IFSCRoundKind::FINAL, $stream, '2025-05-10 14:00', '2025-05-10 15:30', IFSCDiscipline::LEAD);
        $boulder = $this->makeRound("Women's Boulder Final", [IFSCRoundCategory::WOMEN], IFSCRoundKind::FINAL, $stream, '2025-05-10 15:30', '2025-05-10 17:00', IFSCDiscipline::BOULDER);

        $result = $this->merger->merge([$lead, $boulder]);

        $this->assertCount(2, $result);
    }

    #[Test] public function merged_round_is_confirmed_when_any_constituent_round_is_confirmed(): void
    {
        $stream = new LiveStream(url: 'https://youtu.be/abc123');

        $mens = $this->makeRound("Men's Lead Final", [IFSCRoundCategory::MEN], IFSCRoundKind::FINAL, $stream, '2025-05-10 14:00', '2025-05-10 15:30', status: IFSCRoundStatus::PROVISIONAL);
        $womens = $this->makeRound("Women's Lead Final", [IFSCRoundCategory::WOMEN], IFSCRoundKind::FINAL, $stream, '2025-05-10 15:30', '2025-05-10 17:00', status: IFSCRoundStatus::CONFIRMED);

        $result = $this->merger->merge([$mens, $womens]);

        $this->assertSame(IFSCRoundStatus::CONFIRMED, $result[0]->status);
    }

    #[Test] public function unrelated_rounds_without_shared_streams_are_preserved(): void
    {
        $sharedStream = new LiveStream(url: 'https://youtu.be/shared');
        $otherStream = new LiveStream(url: 'https://youtu.be/other');
        $noStream = new LiveStream();

        $mens = $this->makeRound("Men's Lead Final", [IFSCRoundCategory::MEN], IFSCRoundKind::FINAL, $sharedStream, '2025-05-10 14:00', '2025-05-10 15:30');
        $womens = $this->makeRound("Women's Lead Final", [IFSCRoundCategory::WOMEN], IFSCRoundKind::FINAL, $sharedStream, '2025-05-10 15:30', '2025-05-10 17:00');
        $speed = $this->makeRound("Men's Speed Final", [IFSCRoundCategory::MEN], IFSCRoundKind::FINAL, $otherStream, '2025-05-10 18:00', '2025-05-10 19:00');
        $boulderQual = $this->makeRound("Boulder Qualification", [], IFSCRoundKind::QUALIFICATION, $noStream, '2025-05-09 09:00', '2025-05-09 13:00');

        $result = $this->merger->merge([$mens, $womens, $speed, $boulderQual]);

        $this->assertCount(3, $result);
        $this->assertSame('Boulder Qualification', $result[0]->name);
        $this->assertSame("Men's & Women's Lead Final", $result[1]->name);
        $this->assertSame("Men's Speed Final", $result[2]->name);
    }

    private function makeRound(
        string $name,
        array $categories,
        IFSCRoundKind $kind,
        LiveStream $stream,
        string $startTime,
        string $endTime,
        IFSCDiscipline $discipline = IFSCDiscipline::LEAD,
        IFSCRoundStatus $status = IFSCRoundStatus::PROVISIONAL,
    ): IFSCRound {
        return new IFSCRound(
            name: $name,
            categories: $categories,
            disciplines: new IFSCDisciplines([$discipline]),
            kind: $kind,
            liveStream: $stream,
            startTime: new DateTimeImmutable($startTime),
            endTime: new DateTimeImmutable($endTime),
            status: $status,
        );
    }
}
