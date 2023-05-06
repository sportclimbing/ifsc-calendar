<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

final readonly class IFSCSchedule
{
    private function __construct(
        public int $day,
        public Month $month,
        public string $time,
        public int $season,
        public string $cupName,
        public string $streamUrl,
    ) {
    }

    public static function create(
        int $day,
        Month $month,
        string $time,
        int $season,
        string $cupName,
        string $streamUrl,
    ): self  {
        return new self(
            $day,
            $month,
            $time,
            $season,
            $cupName,
            $streamUrl,
        );
    }
}
