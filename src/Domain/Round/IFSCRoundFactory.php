<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use DateTimeImmutable;
use nicoSWD\IfscCalendar\Domain\Stream\IFSCStreamUrl;

final readonly class IFSCRoundFactory
{
    public function create(
        string $name,
        IFSCStreamUrl $streamUrl,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
    ): IFSCRound {
        return new IFSCRound(
            name: $name,
            streamUrl: $streamUrl,
            startTime: $startTime,
            endTime: $endTime,
        );
    }
}
