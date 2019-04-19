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

use League\FlysystemBundle\Adapter\Builder\GcloudAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class GcloudAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new GcloudAdapterDefinitionBuilder();
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

        yield 'api_url' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'api_url' => 'https://storage.googleapis.com',
        ]];

        yield 'full' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'prefix' => 'prefix/path',
            'api_url' => 'https://storage.googleapis.com',
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(GoogleStorageAdapter::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'my_client',
            'bucket' => 'bucket_name',
            'prefix' => 'prefix/path',
            'api_url' => 'https://storage.titouangalopin.com',
        ]);

        $this->assertSame(GoogleStorageAdapter::class, $definition->getClass());

        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));

        /** @var Definition $bucketDefinition */
        $bucketDefinition = $definition->getArgument(1);
        $this->assertInstanceOf(Definition::class, $bucketDefinition);
        $this->assertSame('bucket_name', $bucketDefinition->getArgument(0));
        $this->assertInstanceOf(Reference::class, $bucketDefinition->getFactory()[0]);
        $this->assertSame('my_client', (string) $bucketDefinition->getFactory()[0]);
        $this->assertSame('bucket', $bucketDefinition->getFactory()[1]);

        $this->assertSame('prefix/path', $definition->getArgument(2));
        $this->assertSame('https://storage.titouangalopin.com', $definition->getArgument(3));
    }
}
