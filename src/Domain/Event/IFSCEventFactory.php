<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use Closure;
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
            siteUrl: $this->getSiteUrl($startTime, $id),
            poster: $poster,
            startTime: $startTime,
            endTime: $endTime,
        );
    }

    private function getSiteUrl(DateTimeImmutable $startTime, int $id): string
    {
        $params = [
            'season' => $startTime->format('Y'),
            'event_id' => $id,
        ];

        return preg_replace_callback('~{(?<var_name>season|event_id)}~', $this->replaceVariables($params), $this->siteUrl);
    }

    private function replaceVariables(array $params): Closure
    {
        return static fn (array $match): string => (string) $params[$match['var_name']];
    }
}
