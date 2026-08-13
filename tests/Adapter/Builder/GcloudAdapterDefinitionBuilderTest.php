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

use League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility;
use League\FlysystemBundle\Adapter\Builder\GcloudAdapterDefinitionBuilder;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class GcloudAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): GcloudAdapterDefinitionBuilder
    {
        return new GcloudAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
        ]];

        yield 'full' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'prefix' => 'prefix/path',
            'visibility_handler' => UniformBucketLevelAccessVisibility::class,
            'streamReads' => true,
            'mimeTypeDetector' => 'my_mime_type_detector',
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $bucketDefinition = $definition->getArgument(0);
        $this->assertInstanceOf(Definition::class, $bucketDefinition);
        $this->assertSame('bucket', $bucketDefinition->getArgument(0));
        $this->assertInstanceOf(Reference::class, $bucketDefinition->getFactory()[0]);
        $this->assertSame('my_client', (string) $bucketDefinition->getFactory()[0]);
        $this->assertSame('bucket', $bucketDefinition->getFactory()[1]);

        $this->assertSame('prefix/path', $definition->getArgument(1));
        $this->assertTrue($definition->getArgument(5));

        /** @var Reference $visibilityHandlerReference */
        $visibilityHandlerReference = $definition->getArgument(2);
        $this->assertInstanceOf(Reference::class, $visibilityHandlerReference);
        $this->assertSame(UniformBucketLevelAccessVisibility::class, (string) $visibilityHandlerReference);

        $this->assertInstanceOf(Reference::class, $definition->getArgument(4));
        $this->assertSame('my_mime_type_detector', (string) $definition->getArgument(4));
    }
}
