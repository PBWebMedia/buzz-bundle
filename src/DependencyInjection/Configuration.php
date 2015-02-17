<?php

namespace Pbweb\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @copyright 2015 PB Web Media B.V.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pbweb_buzz');

        $rootNode
            ->children()
                ->scalarNode('client_timeout')->defaultValue(5)->end()
                ->booleanNode('debug')->defaultFalse()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
