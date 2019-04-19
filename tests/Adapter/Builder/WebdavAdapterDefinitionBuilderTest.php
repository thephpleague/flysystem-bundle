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
use League\FlysystemBundle\Adapter\Builder\WebdavAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class WebdavAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new WebdavAdapterDefinitionBuilder();
    }

    public function provideValidOptions()
    {
        yield 'minimal' => [[
            'client' => 'my_client',
        ]];

        yield 'minimal' => [[
            'client' => 'my_client',
            'use_stream_copy' => false,
        ]];

        yield 'prefix' => [[
            'client' => 'my_client',
            'prefix' => 'prefix/path',
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(WebDAVAdapter::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'my_client',
            'prefix' => 'prefix/path',
            'use_stream_copy' => false,
        ]);

        $this->assertSame(WebDAVAdapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('prefix/path', $definition->getArgument(1));
        $this->assertFalse($definition->getArgument(2));
    }
}
