<?php

namespace Klizer\AwsS3Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Klizer\AwsS3Bundle\DependencyInjection\Compiler\TwigPathPass;

class KlizerAwsS3Bundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigPathPass());
    }
}

