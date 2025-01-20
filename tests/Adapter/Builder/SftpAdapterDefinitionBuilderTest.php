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

use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\Visibility;
use League\FlysystemBundle\Adapter\Builder\SftpAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class SftpAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): SftpAdapterDefinitionBuilder
    {
        return new SftpAdapterDefinitionBuilder();
    }

    public function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'host' => 'ftp.example.com',
            'username' => 'username',
        ]];

        yield 'full' => [[
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'port' => 22,
            'root' => '/path/to/root',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'passphrase' => null,
            'hostFingerprint' => null,
            'timeout' => 30,
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options): void
    {
        $this->assertSame(SftpAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
    }

    public function testOptionsBehavior(): void
    {
        $definition = $this->createBuilder()->createDefinition([
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'port' => 22,
            'root' => '/path/to/root',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'passphrase' => null,
            'hostFingerprint' => null,
            'timeout' => 30,
            'directoryPerm' => 0755,
            'permPrivate' => 0700,
            'permPublic' => 0744,
        ], Visibility::PUBLIC);

        $expected = [
            'password' => 'password',
            'port' => 22,
            'root' => '/path/to/root',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'passphrase' => null,
            'hostFingerprint' => null,
            'timeout' => 30,
            'directoryPerm' => 0755,
            'permPrivate' => 0700,
            'permPublic' => 0744,
            'connectivityChecker' => null,
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
        ];

        $this->assertSame(SftpAdapter::class, $definition->getClass());
        $this->assertSame($expected, $definition->getArgument(0)->getArgument(0));
        $this->assertSame($expected['root'], $definition->getArgument(1));
        $this->assertSame(Visibility::PUBLIC, $definition->getArgument(2)->getArgument(1));
    }
}
