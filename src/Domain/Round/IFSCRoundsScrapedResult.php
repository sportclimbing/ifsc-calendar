<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

use nicoSWD\IfscCalendar\Domain\Stream\IFSCStreamUrl;

final readonly class IFSCRoundsScrapedResult
{
    public function __construct(
        public string $roundName,
        public string $startTime,
        public IFSCStreamUrl $streamUrl,
    ) {
    }
}
