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

use AsyncAws\Flysystem\S3\S3FilesystemV1;
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

        yield 'options' => [[
            'client' => 'my_client',
            'bucket' => 'bucket',
            'options' => [
                'ServerSideEncryption' => 'AES256',
            ],
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(S3FilesystemV1::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'my_client',
            'bucket' => 'bucket',
            'prefix' => 'prefix/path',
            'options' => [
                'ServerSideEncryption' => 'AES256',
            ],
        ]);

        $this->assertSame(S3FilesystemV1::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('bucket', $definition->getArgument(1));
        $this->assertSame('prefix/path', $definition->getArgument(2));
        $this->assertSame(['ServerSideEncryption' => 'AES256'], $definition->getArgument(3));
    }
}
