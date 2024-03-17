<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\HttpClient;

interface HttpClientInterface
{
    /** @throws HttpException */
    public function getRetry(string $url): string;

    public function getHeaders(string $url): array;
}
