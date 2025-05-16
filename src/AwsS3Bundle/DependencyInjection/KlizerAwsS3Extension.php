<?php

namespace Klizer\AwsS3Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * It loads service definitions from the Resources/config/services.yml file.
 */
class KlizerAwsS3Extension extends Extension
{
    /**
     * Loads configuration and service definitions for the KlizerAwsS3Bundle.
     *
     * @param array            $configs   The configuration options
     * @param ContainerBuilder $container The container builder
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Define the path to the configuration directory
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        // Load services configuration
        $loader->load('services.yml');
    }
}
