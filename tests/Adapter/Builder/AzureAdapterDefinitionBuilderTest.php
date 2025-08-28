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

use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\FlysystemBundle\Adapter\Builder\AzureAdapterDefinitionBuilder;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AzureAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): AzureAdapterDefinitionBuilder
    {
        return new AzureAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'client' => 'my_client',
            'container' => 'container_name',
        ]];

        yield 'full' => [[
            'client' => 'my_client',
            'container' => 'container_name',
            'prefix' => 'prefix/path',
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $this->assertSame(AzureBlobStorageAdapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('container_name', $definition->getArgument(1));
        $this->assertSame('prefix/path', $definition->getArgument(2));
    }
}
