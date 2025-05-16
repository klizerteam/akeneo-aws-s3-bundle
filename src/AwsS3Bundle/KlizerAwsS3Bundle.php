<?php

namespace Klizer\AwsS3Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Klizer\AwsS3Bundle\DependencyInjection\Compiler\TwigPathPass;

/**
 * Class KlizerAwsS3Bundle
 *
 * This is the main bundle class for the Klizer AWS S3 integration.
 * It registers any compiler passes and initializes required services during the container build process.
 */
class KlizerAwsS3Bundle extends Bundle
{
    /**
     * Builds the bundle by adding custom compiler passes to the container.
     *
     * @param ContainerBuilder $container The container builder instance.
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register the TwigPathPass compiler pass to add custom Twig template paths.
        $container->addCompilerPass(new TwigPathPass());
    }
}