<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Infrastructure\Shell;

final readonly class Command
{
    private const string STD_ERR_FILE = '/tmp/error-output.txt';

    private const string CWD = '/tmp';

    private const int EXIT_SUCCESS = 0;

    /** @throws CommandFailedException */
    public function exec(string $command, ?array $args = []): string
    {
        $process = $this->execCommand($command, $args, $pipes);

        if (!is_resource($process)) {
            throw new CommandFailedException('Unable to start process');
        }

        $response = stream_get_contents($pipes[1]);

        if (empty($response)) {
            throw new CommandFailedException("Process returned empty response");
        }

        $exitCode = proc_close($process);

        if ($exitCode !== self::EXIT_SUCCESS) {
            throw new CommandFailedException("Process exited with return code $exitCode");
        }

        return $response;
    }

    /** @return resource|false */
    private function execCommand(string $command, array $args, mixed &$pipes): mixed
    {
        return proc_open(
            command: sprintf($command, ...$this->escapeShellArgs($args)),
            descriptor_spec: $this->getDescriptorSpec(),
            pipes: $pipes,
            cwd: self::CWD,
            env_vars: [],
        );
    }

    private function escapeShellArgs(array $args): array
    {
        return array_map(static fn (string $arg): string => escapeshellarg($arg), $args);
    }

    /** @return array<int,array<string>> */
    private function getDescriptorSpec(): array
    {
        return [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', self::STD_ERR_FILE, 'a'],
        ];
    }
}
