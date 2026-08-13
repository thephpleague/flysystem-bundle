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

use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Visibility;
use League\FlysystemBundle\Adapter\Builder\MemoryAdapterDefinitionBuilder;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MemoryAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): MemoryAdapterDefinitionBuilder
    {
        return new MemoryAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[]];

        yield 'full' => [[
            'mimeTypeDetector' => 'my_mime_type_detector',
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $this->assertSame(InMemoryFilesystemAdapter::class, $definition->getClass());
        $this->assertSame(Visibility::PUBLIC, $definition->getArgument(0));
        $this->assertInstanceOf(Reference::class, $definition->getArgument(1));
        $this->assertSame('my_mime_type_detector', (string) $definition->getArgument(1));
    }
}
