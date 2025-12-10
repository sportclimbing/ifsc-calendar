<?php declare(strict_types=1);

namespace nicoSWD\IfscCalendar\Application;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class AppContainer
{
    public static function build(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__  . '/../../config/'));
        $loader->load('services.yaml');

        $container->compile();

        return $container;
    }
}
