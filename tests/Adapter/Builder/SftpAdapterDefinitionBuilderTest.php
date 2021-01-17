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

use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\FlysystemBundle\Adapter\Builder\SftpAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class SftpAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new SftpAdapterDefinitionBuilder();
    }

    public function provideValidOptions()
    {
        yield 'minimal' => [[
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
        ]];

        yield 'full' => [[
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'port' => 22,
            'root' => '/path/to/root',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'timeout' => 30,
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(SftpAdapter::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'port' => 22,
            'root' => '/path/to/root',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'timeout' => 30,
            'directoryPerm' => 0755,
            'permPrivate' => 0700,
            'permPublic' => 0744,
        ]);

        $expected = [
            'port' => 22,
            'root' => '/path/to/root',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'timeout' => 30,
            'directoryPerm' => 0755,
            'permPrivate' => 0700,
            'permPublic' => 0744,
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
        ];

        $this->assertSame(SftpAdapter::class, $definition->getClass());
        $this->assertSame($expected, $definition->getArgument(0)->getArgument(0));
        $this->assertSame($expected['root'], $definition->getArgument(1));
    }
}
