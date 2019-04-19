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

use League\Flysystem\Adapter\Local;
use League\FlysystemBundle\Adapter\Builder\LocalAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class LocalAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new LocalAdapterDefinitionBuilder();
    }

    public function provideValidOptions()
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
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(Local::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
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
        ]);

        $this->assertSame(Local::class, $definition->getClass());
        $this->assertSame(__DIR__, $definition->getArgument(0));
        $this->assertSame(LOCK_EX, $definition->getArgument(1));
        $this->assertSame(Local::SKIP_LINKS, $definition->getArgument(2));
        $this->assertSame($permissions, $definition->getArgument(3));
    }
}
