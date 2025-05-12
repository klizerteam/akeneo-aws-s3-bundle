<?php

namespace Klizer\AwsS3Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('klizerAws');
        $rootNode = $treeBuilder->getRootNode();
        
        return $treeBuilder;
    }
}

