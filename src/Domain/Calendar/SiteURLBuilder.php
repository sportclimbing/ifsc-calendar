<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Calendar;

use Closure;
use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;

final readonly class SiteURLBuilder
{
    public function __construct(
        private string $siteUrl,
    ) {
    }

    public function build(IFSCSeasonYear $season, IFSCEventInfo $event): string
    {
        $params = [
            'season' => $season->value,
            'event_id' => $event->eventId,
            'slug' => $event->slug,
        ];

        return preg_replace_callback(
            '~{(?<var_name>season|event_id|slug)}~',
            $this->replaceVariables($params),
            $this->siteUrl,
        );
    }

    /** @param array<string,string|int> $params */
    private function replaceVariables(array $params): Closure
    {
        return static fn (array $match): string => (string) $params[$match['var_name']];
    }
}
