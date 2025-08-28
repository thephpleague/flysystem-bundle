<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\Test;

use League\FlysystemBundle\Adapter\Builder\AdapterDefinitionBuilderInterface;
use League\FlysystemBundle\Adapter\Builder\LazyAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

abstract class AbstractAdapterDefinitionBuilderTest extends TestCase
{
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
    }

    protected function getContainer(): ContainerBuilder
    {
        return $this->container;
    }

    abstract protected function createBuilder(): AdapterDefinitionBuilderInterface;

    abstract protected function assertDefinition(Definition $definition): void;

    /**
     * Default data provider - can be overridden in concrete classes.
     */
    public static function provideValidOptions(): \Generator
    {
        yield 'empty' => [[]];
    }

    public function testGetName(): void
    {
        $builder = $this->createBuilder();
        $this->assertIsString($builder->getName());
        $this->assertNotEmpty($builder->getName());
    }

    public function testGetRequiredPackages(): void
    {
        $builder = $this->createBuilder();
        $packages = $builder->getRequiredPackages();
        $this->assertIsArray($packages);

        foreach ($packages as $class => $packageName) {
            $this->assertIsString($class);
            $this->assertIsString($packageName);
        }
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testConfigurationAndAdapter(array $options, ?string $storageName = null): void
    {
        $builder = $this->createBuilder();

        // Use the data provider key as storage name if available, fallback to 'test_storage'
        $storageName = $storageName ?? $this->dataName() ?? 'test_storage';

        // Test that configuration accepts the options
        $node = new ArrayNodeDefinition('test');
        $builder->addConfiguration($node);
        $tree = $node->getNode();

        try {
            $normalizedOptions = $tree->finalize($options);
            $this->assertIsArray($normalizedOptions);
        } catch (\Exception $e) {
            $this->fail('Configuration should accept valid options: '.$e->getMessage());
        }

        // Test that adapter can be created with the same options
        $adapterId = $builder->createAdapter($this->container, $storageName, $normalizedOptions, null);

        if ($builder instanceof LazyAdapterDefinitionBuilder) {
            // Lazy adapter does not create an adapter service
            $this->assertNull($adapterId);
            $this->assertTrue($this->container->hasDefinition($storageName));
            $definition = $this->container->getDefinition($storageName);
            $this->assertSame('flysystem.adapter.lazy.factory', (string) $definition->getFactory()[0]);
            $this->assertSame($storageName, $definition->getArgument(1));

            return;
        }

        $this->assertIsString($adapterId);
        $this->assertTrue($this->container->hasDefinition($adapterId));

        if ('full' === $storageName) {
            $this->assertDefinition($this->container->getDefinition($adapterId));
        }
    }
}
