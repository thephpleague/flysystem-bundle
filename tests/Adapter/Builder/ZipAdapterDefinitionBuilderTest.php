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

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use League\FlysystemBundle\Adapter\Builder\ZipAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class ZipAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new ZipAdapterDefinitionBuilder();
    }

    public function provideValidOptions()
    {
        yield 'minimal' => [[
            'path' => __DIR__.'/archive.zip',
        ]];

        yield 'prefix' => [[
            'path' => __DIR__.'/archive.zip',
            'prefix' => 'prefix/path',
        ]];

        yield 'archive' => [[
            'path' => __DIR__.'/archive.zip',
            'archive' => 'zip_archive_service',
        ]];

        yield 'full' => [[
            'path' => __DIR__.'/archive.zip',
            'archive' => 'zip_archive_service',
            'prefix' => 'prefix/path',
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(ZipArchiveAdapter::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'path' => __DIR__.'/archive.zip',
            'archive' => 'zip_archive_service',
            'prefix' => 'prefix/path',
        ]);

        $this->assertSame(ZipArchiveAdapter::class, $definition->getClass());
        $this->assertSame(__DIR__.'/archive.zip', $definition->getArgument(0));
        $this->assertInstanceOf(Reference::class, $definition->getArgument(1));
        $this->assertSame('zip_archive_service', (string) $definition->getArgument(1));
        $this->assertSame('prefix/path', $definition->getArgument(2));
    }
}
