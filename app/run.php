<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\IfscCalendar\Application\Command\BuildCommand;
use nicoSWD\IfscCalendar\Application\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context): Application {
    $kernel = new Kernel('prod', (bool) $context['APP_DEBUG']);
    $kernel->boot();

    /** @var BuildCommand $command */
    $command = $kernel->getContainer()->get(id: BuildCommand::class);

    $application = new Application($kernel);
    $application->add($command);
    $application->setDefaultCommand($command->getName(), isSingleCommand: true);

    return $application;
};
