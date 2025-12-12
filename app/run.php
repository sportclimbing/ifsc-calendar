<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
use nicoSWD\IfscCalendar\Application\Command\BuildCommand;
use nicoSWD\IfscCalendar\Application\AppContainer;
use Symfony\Component\Console\Application;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context): Application {
    $container = AppContainer::build();

    /** @var BuildCommand $command */
    $command = $container->get(BuildCommand::class);

    $application = new Application('ifsc-calendar', '1.0.0');
    $application->setAutoExit(false);

    $application->addCommands([$command]);
    $application->setDefaultCommand($command->getName(), true);

    if ($context['APP_DEBUG'] ?? false) {
        $application->setCatchExceptions(false);
    }

    return $application;
};
