<?php

namespace Pbweb\BuzzBundle\Tests\DependencyInjection;

use Buzz\Browser;
use Pbweb\BuzzBundle\DependencyInjection\PbwebBuzzExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class PbwebBuzzExtensionTest
 *
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

    public function testDebug()
    {
        $config = [['debug' => true]];

        $extension = new PbwebBuzzExtension();
        $extension->load($config, $container = new ContainerBuilder());

        $this->assertEquals('pbweb_buzz.debug.traceable_browser', (string) $container->getAlias('pbweb_buzz.browser'));
        $this->assertEquals('pbweb_buzz.debug.traceable_browser', (string) $container->getAlias('buzz'));
    }
}
