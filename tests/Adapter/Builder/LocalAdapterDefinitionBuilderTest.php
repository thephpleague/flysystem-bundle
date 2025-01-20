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

use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Visibility;
use League\FlysystemBundle\Adapter\Builder\LocalAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class LocalAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): LocalAdapterDefinitionBuilder
    {
        return new LocalAdapterDefinitionBuilder();
    }

    public function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'directory' => __DIR__,
        ]];

        yield 'lock' => [[
            'directory' => __DIR__,
            'lock' => 0,
        ]];

        yield 'skip_links' => [[
            'directory' => __DIR__,
            'skip_links' => true,
        ]];

        yield 'permissions_file_public' => [[
            'directory' => __DIR__,
            'permissions' => [
                'file' => [
                    'public' => 0700,
                ],
            ],
        ]];

        yield 'permissions_dir_private' => [[
            'directory' => __DIR__,
            'permissions' => [
                'dir' => [
                    'private' => 0755,
                ],
            ],
        ]];

        yield 'lazy_root_creation_enabled' => [[
            'directory' => __DIR__,
            'lazy_root_creation' => true,
        ]];

        yield 'lazy_root_creation_disabled' => [[
            'directory' => __DIR__,
            'lazy_root_creation' => false,
        ]];

        yield 'full' => [[
            'directory' => __DIR__,
            'lock' => 0,
            'skip_links' => true,
            'permissions' => [
                'file' => [
                    'public' => 0755,
                    'private' => 0755,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0755,
                ],
            ],
            'lazy_root_creation' => true,
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options): void
    {
        $this->assertSame(LocalFilesystemAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
    }

    public function testOptionsBehavior(): void
    {
        $permissions = [
            'file' => [
                'public' => 0755,
                'private' => 0755,
            ],
            'dir' => [
                'public' => 0755,
                'private' => 0755,
            ],
        ];

        $definition = $this->createBuilder()->createDefinition([
            'directory' => __DIR__,
            'lock' => LOCK_EX,
            'skip_links' => true,
            'permissions' => $permissions,
            'lazy_root_creation' => true,
        ], Visibility::PUBLIC);

        $this->assertSame(LocalFilesystemAdapter::class, $definition->getClass());
        $this->assertSame(__DIR__, $definition->getArgument(0));
        $this->assertSame($permissions, $definition->getArgument(1)->getArgument(0));
        $this->assertSame(LOCK_EX, $definition->getArgument(2));
        $this->assertSame(LocalFilesystemAdapter::SKIP_LINKS, $definition->getArgument(3));
        $this->assertSame(true, $definition->getArgument(5));
        $this->assertSame(Visibility::PUBLIC, $definition->getArgument(1)->getArgument(1));
    }
}
