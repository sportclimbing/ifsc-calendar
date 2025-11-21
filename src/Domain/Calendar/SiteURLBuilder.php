<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar;

use Closure;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;

final readonly class SiteURLBuilder
{
    public function __construct(
        private string $siteUrl,
    ) {
    }

    public function build(IFSCSeasonYear $season, int $eventId): string
    {
        $params = [
            'season' => $season->value,
            'event_id' => $eventId,
        ];

        return preg_replace_callback(
            '~{(?<var_name>season|event_id)}~',
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
