<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\HttpClient;

interface HttpClientInterface
{
    /**
     * @param array<string,mixed> $options
     * @throws HttpException
     */
    public function getRetry(string $url, array $options = []): string;

    /**
     * @param array<string,mixed> $options
     * @return array<array<string>>
     * @throws HttpException
     */
    public function getHeaders(string $url, array $options = []): array;

    /**
     * @param array<string,mixed> $options
     * @throws HttpException
     */
    public function get(string $url, array $options): string;

    /** @throws HttpException */
    public function downloadFile(string $url, string $saveAs): void;
}
