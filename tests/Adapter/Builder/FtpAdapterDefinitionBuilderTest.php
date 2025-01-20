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
use League\Flysystem\Visibility;
use League\FlysystemBundle\Adapter\Builder\FtpAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class FtpAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): FtpAdapterDefinitionBuilder
    {
        return new FtpAdapterDefinitionBuilder();
    }

    public function provideValidOptions(): \Generator
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
    public function testCreateDefinition($options): void
    {
        $this->assertSame(FtpAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
    }

    public function testOptionsBehavior(): void
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
        ], Visibility::PUBLIC);

        $expected = [
            'port' => 21,
            'root' => '/path/to/root',
            'passive' => true,
            'ssl' => true,
            'timeout' => 30,
            'utf8' => false,
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ],
            ],
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'transferMode' => null,
            'systemType' => null,
            'timestampsOnUnixListingsEnabled' => false,
            'ignorePassiveAddress' => true,
            'recurseManually' => true,
        ];

        $this->assertSame(FtpAdapter::class, $definition->getClass());
        $this->assertSame($expected, $definition->getArgument(0)->getArgument(0));
        $this->assertSame(Visibility::PUBLIC, $definition->getArgument(3)->getArgument(1));
    }
}
