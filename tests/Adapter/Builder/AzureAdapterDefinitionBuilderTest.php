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
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class AzureAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): AzureAdapterDefinitionBuilder
    {
        return new AzureAdapterDefinitionBuilder();
    }

    public function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'client' => 'my_client',
            'container' => 'container_name',
        ]];

        yield 'prefix' => [[
            'client' => 'my_client',
            'container' => 'container_name',
            'prefix' => 'prefix/path',
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options): void
    {
        $this->assertSame(AzureBlobStorageAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
    }

    public function testOptionsBehavior(): void
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'my_client',
            'container' => 'container_name',
            'prefix' => 'prefix/path',
        ], null);

        $this->assertSame(AzureBlobStorageAdapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('container_name', $definition->getArgument(1));
        $this->assertSame('prefix/path', $definition->getArgument(2));
    }
}
