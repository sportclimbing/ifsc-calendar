<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

final readonly class IFSCEventSlug
{
    public function create(string $eventName): string
    {
        $eventName = mb_convert_encoding($eventName, mb_detect_encoding($eventName, strict: true), 'UTF-8');
        $eventName = mb_strtolower($eventName);
        $eventName = strtr($eventName, ['ç' => 'c']);

        return preg_replace('~\W+~u', '-', $eventName);
    }
}
