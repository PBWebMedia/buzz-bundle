<?php

namespace Pbweb\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pbweb_buzz');

        // For BC with symfony/config < 4.2
        if (method_exists($treeBuilder, 'getRootNode')) {
            /** @var ArrayNodeDefinition $rootNode */
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('pbweb_buzz');
        }

        $rootNode
            ->children()
                ->scalarNode('client_timeout')->defaultValue(5)->end()
                ->booleanNode('debug')->defaultFalse()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
