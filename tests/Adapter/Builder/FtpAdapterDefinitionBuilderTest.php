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

use League\Flysystem\Ftp\FtpAdapter;
use League\FlysystemBundle\Adapter\Builder\FtpAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class FtpAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new FtpAdapterDefinitionBuilder();
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
            'port' => 21,
            'root' => '/path/to/root',
            'passive' => true,
            'ssl' => true,
            'timeout' => 30,
            'ignore_passive_address' => true,
            'utf8' => false,
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options)
    {
        $this->assertSame(FtpAdapter::class, $this->createBuilder()->createDefinition($options)->getClass());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'port' => 21,
            'root' => '/path/to/root',
            'passive' => true,
            'ssl' => true,
            'timeout' => 30,
            'ignore_passive_address' => true,
            'utf8' => false,
        ]);

        $expected = [
            'port' => 21,
            'root' => '/path/to/root',
            'passive' => true,
            'ssl' => true,
            'timeout' => 30,
            'utf8' => false,
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'ignorePassiveAddress' => true,
        ];

        $this->assertSame(FtpAdapter::class, $definition->getClass());
        $this->assertSame($expected, $definition->getArgument(0)->getArgument(0));
    }
}
