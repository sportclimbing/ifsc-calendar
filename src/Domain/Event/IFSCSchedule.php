<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

final readonly class IFSCSchedule
{
    private function __construct(
        public string $cupName,
        public string $streamUrl,
        public IFSCEventDuration $duration,
    ) {
    }

    /** @throws InvalidURLException */
    public static function create(
        int $day,
        Month $month,
        string $time,
        string $timeZone,
        IFSCSeasonYear $season,
        string $cupName,
        string $streamUrl = '',
    ): self  {
        self::assertValidUrl($streamUrl);

        return new self(
            $cupName,
            $streamUrl,
            IFSCEventDuration::create(
                day: $day,
                month: $month,
                time: $time,
                timeZone: $timeZone,
                season: $season,
            ),
        );
    }

    /** @throws InvalidURLException */
    private static function assertValidUrl(string $streamUrl): void
    {
        if (!empty($streamUrl) && !filter_var($streamUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidURLException("Invalid URL: {$streamUrl}");
        }
    }
}
