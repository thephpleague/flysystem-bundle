<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\Adapter\Builder;

use League\Flysystem\Replicate\ReplicateAdapter;
use League\FlysystemBundle\Adapter\Builder\ReplicateAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class ReplicateAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new ReplicateAdapterDefinitionBuilder();
    }

    public function provideValidOptions()
    {
        yield 'minimal' => [[
            'source' => 'my_source',
            'replica' => 'my_replica',
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(ReplicateAdapter::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'source' => 'my_source',
            'replica' => 'my_replica',
        ]);

        $this->assertSame(ReplicateAdapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('flysystem.adapter.my_source', (string) $definition->getArgument(0));
        $this->assertInstanceOf(Reference::class, $definition->getArgument(1));
        $this->assertSame('flysystem.adapter.my_replica', (string) $definition->getArgument(1));
    }
}
