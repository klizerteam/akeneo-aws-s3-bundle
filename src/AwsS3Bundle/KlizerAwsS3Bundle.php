<?php

namespace Klizer\AwsS3Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KlizerAwsS3Bundle extends Bundle
{
   public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}

