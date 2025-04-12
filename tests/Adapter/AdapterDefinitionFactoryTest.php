<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\Adapter;

use League\FlysystemBundle\Adapter\AdapterDefinitionFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;

class AdapterDefinitionFactoryTest extends TestCase
{
    public static function provideConfigOptions(): \Generator
    {
        yield 'fs_async_aws' => [
            'adapter' => 'asyncaws',
            'options' => [
                'client' => 'aws_client_service',
                'bucket' => 'bucket_name',
                'prefix' => 'optional/path/prefix',
            ],
        ];

        yield 'fs_aws' => [
            'adapter' => 'aws',
            'options' => [
                'client' => 'aws_client_service',
                'bucket' => 'bucket_name',
                'prefix' => 'optional/path/prefix',
            ],
        ];

        yield 'fs_azure' => [
            'adapter' => 'azure',
            'options' => [
                'client' => 'azure_client_service',
                'container' => 'container_name',
                'prefix' => 'optional/path/prefix',
            ],
        ];

        yield 'fs_ftp' => [
            'adapter' => 'ftp',
            'options' => [
                'host' => 'ftp.example.com',
                'username' => 'username',
                'password' => 'password',
                'port' => 21,
                'root' => '/path/to/root',
                'passive' => true,
                'ssl' => true,
                'timeout' => 30,
                'ignore_passive_address' => true,
            ],
        ];

        yield 'fs_gcloud' => [
            'adapter' => 'gcloud',
            'options' => [
                'client' => 'gcloud_client_service',
                'bucket' => 'bucket_name',
                'prefix' => 'optional/path/prefix',
            ],
        ];

        yield 'fs_local' => [
            'adapter' => 'local',
            'options' => [
                'directory' => '%kernel.project_dir%/storage',
                'lock' => 0,
                'skip_links' => false,
                'lazy_root_creation' => false,
                'permissions' => [
                    'file' => [
                        'public' => '0744',
                        'private' => '0700',
                    ],
                    'dir' => [
                        'public' => '0755',
                        'private' => '0700',
                    ],
                ],
            ],
        ];

        yield 'fs_memory' => [
            'adapter' => 'memory',
            'options' => [],
        ];

        yield 'fs_sftp' => [
            'adapter' => 'sftp',
            'options' => [
                'host' => 'example.com',
                'port' => 22,
                'username' => 'username',
                'password' => 'password',
                'privateKey' => 'path/to/or/contents/of/privatekey',
                'root' => '/path/to/root',
                'timeout' => 10,
                'preferredAlgorithms' => [
                    'hostkey' => ['rsa-sha2-256', 'ssh-rsa'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideConfigOptions
     */
    public static function testCreateDefinition($name, $options): void
    {
        $factory = new AdapterDefinitionFactory([]);

        $definition = $factory->createDefinition($name, $options);
        self::assertInstanceOf(Definition::class, $definition);
    }
}
