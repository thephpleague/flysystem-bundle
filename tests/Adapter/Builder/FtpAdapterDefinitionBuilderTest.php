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
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;

class FtpAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): FtpAdapterDefinitionBuilder
    {
        return new FtpAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
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
            'system_type' => 'unix',
            'recurse_manually' => false,
            'use_raw_list_options' => true,
            'connectivityChecker' => 'my_checker',
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $expected = [
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
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
            'transferMode' => null,
            'systemType' => 'unix',
            'ignorePassiveAddress' => true,
            'timestampsOnUnixListingsEnabled' => false,
            'recurseManually' => false,
            'useRawListOptions' => true,
        ];

        $this->assertSame(FtpAdapter::class, $definition->getClass());
        $this->assertSame($expected, $definition->getArgument(0)->getArgument(0));
        $this->assertSame('my_checker', (string) $definition->getArgument(2));
        $this->assertSame(Visibility::PRIVATE, $definition->getArgument(3)->getArgument(1));
    }
}
