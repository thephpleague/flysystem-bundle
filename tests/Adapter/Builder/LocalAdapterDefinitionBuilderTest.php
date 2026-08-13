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
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LocalAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): LocalAdapterDefinitionBuilder
    {
        return new LocalAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'directory' => __DIR__,
        ]];

        yield 'full' => [[
            'directory' => __DIR__,
            'lock' => LOCK_EX,
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
            'mimeTypeDetector' => 'my_mime_type_detector',
        ]];
    }

    protected function assertDefinition(Definition $definition): void
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

        $this->assertSame(LocalFilesystemAdapter::class, $definition->getClass());
        $this->assertSame(__DIR__, $definition->getArgument(0));
        $this->assertSame($permissions, $definition->getArgument(1)->getArgument(0));
        $this->assertSame(Visibility::PRIVATE, $definition->getArgument(1)->getArgument(1));
        $this->assertSame(LOCK_EX, $definition->getArgument(2));
        $this->assertSame(LocalFilesystemAdapter::SKIP_LINKS, $definition->getArgument(3));
        $this->assertInstanceOf(Reference::class, $definition->getArgument(4));
        $this->assertSame('my_mime_type_detector', (string) $definition->getArgument(4));
        $this->assertSame(true, $definition->getArgument(5));
    }
}
