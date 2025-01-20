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

use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\Visibility;
use League\FlysystemBundle\Adapter\Builder\AsyncAwsAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class AsyncAwsAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): AsyncAwsAdapterDefinitionBuilder
    {
        return new AsyncAwsAdapterDefinitionBuilder();
    }

    public function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
        ]];

        yield 'prefix' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'prefix' => 'prefix/path',
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options): void
    {
        $definition = $this->createBuilder()->createDefinition($options, Visibility::PRIVATE);
        $this->assertSame(AsyncAwsS3Adapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('bucket', $definition->getArgument(1));
        $this->assertSame(Visibility::PRIVATE, $definition->getArgument(3)->getArgument(0));
    }
}
