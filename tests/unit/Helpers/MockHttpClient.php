<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\tests\Helpers;

use DateTimeInterface;
use nicoSWD\IfscCalendar\Domain\HttpClient\HttpClientInterface;

trait MockHttpClient
{
    protected function mockClientReturningFile(string $fileName): HttpClientInterface
    {
        return new class ($this->htmlFile($fileName)) implements HttpClientInterface {
            public function __construct(
                private readonly string $fileName,
            ) {
            }

            public function get(string $url): string
            {
                return file_get_contents($this->fileName);
            }

            public function getRetry(string $url): string
            {
                return $this->get($url);
            }
        };
    }

    protected function htmlFile(string $fileName): string
    {
        return __DIR__ . "/../../html/{$fileName}";
    }

    protected function formatDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(DateTimeInterface::RFC3339);
    }
}
