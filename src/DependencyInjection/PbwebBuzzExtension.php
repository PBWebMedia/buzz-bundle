<?php

namespace Pbweb\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class PbwebBuzzExtension
 *
 * @copyright 2015 PB Web Media B.V.
 */
class PbwebBuzzExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $root = 'pbweb_buzz.';
        $container->setParameter($root . 'client_timeout', $config['client_timeout']);

        if ($config['debug']) {
            $loader->load('debug.yml');
        }
    }
}
