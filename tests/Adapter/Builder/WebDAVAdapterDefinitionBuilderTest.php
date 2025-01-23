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

use League\Flysystem\Visibility;
use League\Flysystem\WebDAV\WebDAVAdapter;
use League\FlysystemBundle\Adapter\Builder\WebDAVAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @author Kévin Dunglas <kevin@dunglas.dev>
 */
class WebDAVAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): WebDAVAdapterDefinitionBuilder
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
            'visibility_handling' => WebDAVAdapter::ON_VISIBILITY_THROW_ERROR,
            'manual_copy' => false,
            'manual_move' => false,
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition(array $options): void
    {
        $this->assertSame(WebDAVAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
    }

    public function testOptionsBehavior(): void
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'webdav_client',
            'prefix' => 'optional/path/prefix',
            'visibility_handling' => WebDAVAdapter::ON_VISIBILITY_IGNORE,
            'manual_copy' => false,
            'manual_move' => false,
        ], Visibility::PUBLIC);

        $this->assertSame(WebDAVAdapter::class, $definition->getClass());
        $this->assertSame('webdav_client', (string) $definition->getArgument(0));
        $this->assertSame('optional/path/prefix', $definition->getArgument(1));
        $this->assertSame(WebDAVAdapter::ON_VISIBILITY_IGNORE, $definition->getArgument(2));
        $this->assertFalse($definition->getArgument(3));
        $this->assertFalse($definition->getArgument(4));
    }
}
