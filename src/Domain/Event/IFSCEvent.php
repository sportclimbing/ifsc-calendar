<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

final class IFSCEvent
{
    /** @param IFSCRound[] $rounds */
    public function __construct(
        public readonly int $season,
        public readonly int $eventId,
        public readonly string $timeZone,
        public readonly string $eventName,
        public readonly string $location,
        public readonly string $country,
        public readonly ?string $poster,
        public readonly string $siteUrl,
        public readonly string $startsAt,
        public readonly string $endsAt,
        public readonly array $disciplines,
        public array $rounds,
    ) {
    }
}
