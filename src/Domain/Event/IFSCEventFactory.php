<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use DateTimeImmutable;

final readonly class IFSCEventFactory
{
    public function __construct(
        private string $siteUrl,
    ) {
    }

    public function create(
        string $name,
        int $id,
        string $description,
        string $streamUrl,
        string $poster,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
    ): IFSCEvent {
        return new IFSCEvent(
            name: $name,
            id: $id,
            description: $description,
            streamUrl: $streamUrl,
            siteUrl: sprintf($this->siteUrl, $startTime->format('Y'), $id),
            poster: $poster,
            startTime: $startTime,
            endTime: $endTime,
        );
    }
}
