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

use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Visibility;
use League\FlysystemBundle\Adapter\Builder\AwsAdapterDefinitionBuilder;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AwsAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): AwsAdapterDefinitionBuilder
    {
        return new AwsAdapterDefinitionBuilder();
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
            'options' => [
                'ServerSideEncryption' => 'AES256',
            ],
            'streamReads' => false,
            'mimeTypeDetector' => 'my_mime_type_detector',
            'forwardedOptions' => ['ServerSideEncryption'],
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $this->assertSame(AwsS3V3Adapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('bucket', $definition->getArgument(1));
        $this->assertSame('prefix/path', $definition->getArgument(2));
        $this->assertSame(Visibility::PUBLIC, $definition->getArgument(3)->getArgument(0));
        $this->assertInstanceOf(Reference::class, $definition->getArgument(4));
        $this->assertSame('my_mime_type_detector', (string) $definition->getArgument(4));
        $this->assertSame(['ServerSideEncryption' => 'AES256'], $definition->getArgument(5));
        $this->assertFalse($definition->getArgument(6));
        $this->assertSame(['ServerSideEncryption'], $definition->getArgument(7));
    }
}
