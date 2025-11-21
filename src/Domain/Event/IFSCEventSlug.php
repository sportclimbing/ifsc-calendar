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
        return $eventName
            |> $this->toUtf8(...)
            |> $this->toLowerCase(...)
            |> $this->normalize(...)
            |> $this->replaceNonAlphaChars(...);
    }

    private function toUtf8(string $eventName): string
    {
        return mb_convert_encoding($eventName, $this->detectEncoding($eventName), 'UTF-8');
    }

    private function toLowerCase(string $eventName): string
    {
        return mb_strtolower($eventName);
    }

    private function detectEncoding(string $eventName): string
    {
        return mb_detect_encoding($eventName, strict: true);
    }

    private function normalize(string $eventName): string
    {
        return strtr($eventName, ['ç' => 'c']);
    }

    private function replaceNonAlphaChars(string $eventName): string
    {
        return preg_replace('~\W+~u', '-', $eventName);
    }
}
