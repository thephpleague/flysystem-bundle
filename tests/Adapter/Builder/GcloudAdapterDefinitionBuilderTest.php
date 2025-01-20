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

use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler;
use League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility;
use League\FlysystemBundle\Adapter\Builder\GcloudAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class GcloudAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): GcloudAdapterDefinitionBuilder
    {
        return new GcloudAdapterDefinitionBuilder();
    }

    public function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
        ]];

        yield 'full' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'prefix' => 'prefix/path',
        ]];

        yield 'portable visibility handler' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'visibility_handler' => PortableVisibilityHandler::class,
        ]];

        yield 'uniform visibility handler' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'visibility_handler' => UniformBucketLevelAccessVisibility::class,
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options): void
    {
        $this->assertSame(GoogleCloudStorageAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
    }

    public function testOptionsBehavior(): void
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'my_client',
            'bucket' => 'bucket_name',
            'prefix' => 'prefix/path',
            'visibility_handler' => UniformBucketLevelAccessVisibility::class,
        ], null);

        $this->assertSame(GoogleCloudStorageAdapter::class, $definition->getClass());

        /** @var Definition $bucketDefinition */
        $bucketDefinition = $definition->getArgument(0);
        $this->assertInstanceOf(Definition::class, $bucketDefinition);
        $this->assertSame('bucket_name', $bucketDefinition->getArgument(0));
        $this->assertInstanceOf(Reference::class, $bucketDefinition->getFactory()[0]);
        $this->assertSame('my_client', (string) $bucketDefinition->getFactory()[0]);
        $this->assertSame('bucket', $bucketDefinition->getFactory()[1]);

        $this->assertSame('prefix/path', $definition->getArgument(1));

        /** @var Reference $visibilityHandlerReference */
        $visibilityHandlerReference = $definition->getArgument(2);
        $this->assertInstanceOf(Reference::class, $visibilityHandlerReference);
    }
}
