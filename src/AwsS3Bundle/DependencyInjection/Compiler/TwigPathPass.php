<?php

namespace Klizer\AwsS3Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigPathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loaderId = 'twig.loader.native_filesystem';
        if (!$container->hasDefinition($loaderId)) {
            error_log('Twig loader not found');
            return;
        }

        $definition = $container->getDefinition($loaderId);

        $viewPath = dirname(__DIR__, 6) . '/klizer/akeneo-aws-s3-bundle/src/AwsS3Bundle/Resources/views';

        if (is_dir($viewPath)) {
            $definition->addMethodCall('addPath', [$viewPath, 'klizer_aws']);
        } else {
            error_log("Twig path does NOT exist: $viewPath");
        }
    }
}

