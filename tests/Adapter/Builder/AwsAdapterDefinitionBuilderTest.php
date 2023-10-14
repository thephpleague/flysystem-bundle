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
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class AwsAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): AwsAdapterDefinitionBuilder
    {
        return new AwsAdapterDefinitionBuilder();
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
        $this->assertSame(AwsS3V3Adapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
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
            'streamReads' => false,
        ], Visibility::PRIVATE);

        $this->assertSame(AwsS3V3Adapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('bucket', $definition->getArgument(1));
        $this->assertSame('prefix/path', $definition->getArgument(2));
        $this->assertSame(['ServerSideEncryption' => 'AES256'], $definition->getArgument(5));
        $this->assertFalse($definition->getArgument(6));
        $this->assertSame(Visibility::PRIVATE, $definition->getArgument(3)->getArgument(0));
    }
}
