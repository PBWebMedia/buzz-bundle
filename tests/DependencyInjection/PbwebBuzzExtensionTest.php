<?php

namespace Pbweb\BuzzBundle\DependencyInjection;

use Buzz\Browser;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class PbwebBuzzExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testRoot()
    {
        $config = [[]];

        $root = 'pbweb_buzz.';
        $extension = new PbwebBuzzExtension();
        $extension->load($config, $container = new ContainerBuilder());

        foreach ($container->getDefinitions() as $id => $definition) {
            if ($id == 'service_container') {
                continue;
            }
            $this->assertStringStartsWith($root, $id);
        }

        foreach ($container->getAliases() as $id => $definition) {
            if (strpos($id, '\\') !== -1) {
                continue;
            }
            $this->assertStringStartsWith($root, $id);
        }

        foreach ($container->getParameterBag()->all() as $id => $definition) {
            $this->assertStringStartsWith($root, $id);
        }
    }

    public function testBuzzAlias()
    {
        $config = [[]];

        $extension = new PbwebBuzzExtension();
        $extension->load($config, $container = new ContainerBuilder());

        $result = $container->get('buzz');

        $this->assertInstanceOf(Browser::class, $result);
    }

    public function testSettings()
    {
        $config = [[
            'client_timeout' => 120,
        ]];

        $extension = new PbwebBuzzExtension();
        $extension->load($config, $container = new ContainerBuilder());

        $this->assertSame(120, $container->getParameter('pbweb_buzz.client_timeout'));
    }

    public function testDebug()
    {
        $config = [['debug' => true]];

        $extension = new PbwebBuzzExtension();
        $extension->load($config, $container = new ContainerBuilder());

        $this->assertEquals('pbweb_buzz.debug.traceable_browser', (string) $container->getAlias('pbweb_buzz.browser'));
        $this->assertEquals('pbweb_buzz.debug.traceable_browser', (string) $container->getAlias('buzz'));
    }
}
