<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\Adapter;

use League\FlysystemBundle\Adapter\AdapterDefinitionFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Yaml\Yaml;

class AdapterDefinitionFactoryTest extends TestCase
{
    public function provideConfigOptions()
    {
        $config = Yaml::parseFile(__DIR__.'/options.yaml');

        foreach ($config as $fs) {
            yield $fs['adapter'] => [$fs['adapter'], $fs['options'] ?? []];
        }
    }

    /**
     * @dataProvider provideConfigOptions
     */
    public function testCreateDefinition($name, $options)
    {
        $factory = new AdapterDefinitionFactory();

        $definition = $factory->createDefinition($name, $options);
        $this->assertInstanceOf(Definition::class, $definition);
    }
}
