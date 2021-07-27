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
use League\FlysystemBundle\Adapter\Builder\AsyncAwsAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @requires PHP 7.2
 */
class AsyncAwsAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new AsyncAwsAdapterDefinitionBuilder();
    }

    public function provideValidOptions()
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
    public function testCreateDefinition($options)
    {
        $definition = $this->createBuilder()->createDefinition($options);
        $this->assertSame(AsyncAwsS3Adapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('bucket', $definition->getArgument(1));
    }
}
