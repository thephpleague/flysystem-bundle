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

use League\FlysystemBundle\Adapter\Builder\DropboxAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Symfony\Component\DependencyInjection\Reference;

class DropboxAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new DropboxAdapterDefinitionBuilder();
    }

    public function provideValidOptions()
    {
        yield 'minimal' => [[
            'client' => 'my_client',
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
        $this->assertSame(DropboxAdapter::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'my_client',
            'prefix' => 'prefix/path',
        ]);

        $this->assertSame(DropboxAdapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_client', (string) $definition->getArgument(0));
        $this->assertSame('prefix/path', $definition->getArgument(1));
    }
}
