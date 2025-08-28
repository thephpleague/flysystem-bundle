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

use League\Flysystem\WebDAV\WebDAVAdapter;
use League\FlysystemBundle\Adapter\Builder\WebDAVAdapterDefinitionBuilder;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Kévin Dunglas <kevin@dunglas.dev>
 */
class WebDAVAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): WebDAVAdapterDefinitionBuilder
    {
        return new WebDAVAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'client' => 'webdav_client',
        ]];

        yield 'full' => [[
            'client' => 'webdav_client',
            'prefix' => 'optional/path/prefix',
            'visibility_handling' => WebDAVAdapter::ON_VISIBILITY_IGNORE,
            'manual_copy' => false,
            'manual_move' => false,
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $this->assertSame(WebDAVAdapter::class, $definition->getClass());
        $this->assertSame('webdav_client', (string) $definition->getArgument(0));
        $this->assertSame('optional/path/prefix', $definition->getArgument(1));
        $this->assertSame(WebDAVAdapter::ON_VISIBILITY_IGNORE, $definition->getArgument(2));
        $this->assertFalse($definition->getArgument(3));
        $this->assertFalse($definition->getArgument(4));
    }
}
