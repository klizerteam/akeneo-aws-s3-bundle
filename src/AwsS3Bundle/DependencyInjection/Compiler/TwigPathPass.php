<?php

namespace Klizer\AwsS3Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TwigPathPass
 *
 * Adds a custom Twig template path for the Klizer AWS S3 Bundle.
 */
class TwigPathPass implements CompilerPassInterface
{
    /**
     * Adds the custom template path to the Twig native filesystem loader.
     *
     * @param ContainerBuilder $container The container builder
     */
    public function process(ContainerBuilder $container): void
    {
        $loaderId = 'twig.loader.native_filesystem';

        // Check if Twig loader is defined
        if (!$container->hasDefinition($loaderId)) {
            error_log('Twig loader not found.');
            return;
        }

        $definition = $container->getDefinition($loaderId);

        // Define the path to the bundle's Twig templates
        $viewPath = dirname(__DIR__, 6) . '/klizer/akeneo-aws-s3-bundle/src/AwsS3Bundle/Resources/views';

        if (is_dir($viewPath)) {
            // Add the view path to the Twig loader
            $definition->addMethodCall('addPath', [$viewPath, 'klizer_aws']);
        } else {
            error_log("Twig path does NOT exist: $viewPath");
        }
    }
}
