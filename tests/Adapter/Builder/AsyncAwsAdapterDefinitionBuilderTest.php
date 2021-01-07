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

use AsyncAws\Flysystem\S3\S3FilesystemV2;
use League\FlysystemBundle\Adapter\Builder\AsyncAwsAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

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
        if (!class_exists(S3FilesystemV2::class)) {
            $this->markTestSkipped();
        }

        $this->assertSame(S3FilesystemV2::class, $this->createBuilder()->createDefinition($options)->getClass());
    }
}
