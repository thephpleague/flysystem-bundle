<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->extension('flysystem', [
        'storages' => [
            'fs_asyncaws' => [
                'asyncaws' => [
                    'client' => 'asyncaws_client_service',
                    'bucket' => '%env(AWS_BUCKET)%',
                    'prefix' => 'optional/path/prefix',
                ],
            ],
            'fs_aws' => [
                'aws' => [
                    'client' => 'aws_client_service',
                    'bucket' => '%env(AWS_BUCKET)%',
                    'prefix' => 'optional/path/prefix',
                ],
                'visibility' => 'private',
                'retain_visibility' => false,
            ],
            'fs_azure' => [
                'azure' => [
                    'client' => 'azure_client_service',
                    'container' => 'container_name',
                    'prefix' => 'optional/path/prefix',
                ],
            ],
            'fs_custom' => [
                'service' => 'custom_adapter',
            ],
            'fs_ftp' => [
                'ftp' => [
                    'host' => 'ftp.example.com',
                    'username' => 'username',
                    'password' => 'password',
                    'port' => '%env(int:FTP_PORT)%',
                    'root' => '/path/to/root',
                    'passive' => true,
                    'ssl' => true,
                    'timeout' => 30,
                    'utf8' => false,
                ],
            ],
            'fs_gcloud' => [
                'gcloud' => [
                    'client' => 'gcloud_client_service',
                    'bucket' => 'bucket_name',
                    'prefix' => 'optional/path/prefix',
                ],
            ],
            'fs_lazy' => [
                'lazy' => [
                    'source' => '%env(LAZY_SOURCE)%',
                ],
            ],
            'fs_local' => [
                'local' => [
                    'directory' => '/tmp/storage',
                    'lock' => 0,
                    'skip_links' => false,
                    'permissions' => [
                        'file' => [
                            'public' => 0744,
                            'private' => 0700,
                        ],
                        'dir' => [
                            'public' => 0755,
                            'private' => 0700,
                        ],
                    ],
                ],
            ],
            'fs_memory' => [
                'memory' => null,
            ],
            'fs_sftp' => [
                'sftp' => [
                    'host' => 'example.com',
                    'port' => 22,
                    'username' => 'username',
                    'password' => 'password',
                    'privateKey' => 'path/to/or/contents/of/privatekey',
                    'root' => '/path/to/root',
                    'timeout' => 10,
                ],
            ],
            'fs_public_url' => [
                'local' => [
                    'directory' => '/tmp/storage',
                ],
                'public_url' => 'https://example.org/assets/',
            ],
            'fs_public_urls' => [
                'local' => [
                    'directory' => '/tmp/storage',
                ],
                'public_url' => [
                    'https://cdn1.example.org/',
                    'https://cdn2.example.org/',
                    'https://cdn3.example.org/',
                ],
            ],
            'fs_url_generator' => [
                'local' => [
                    'directory' => '/tmp/storage',
                ],
                'public_url_generator' => 'flysystem.test.public_url_generator',
                'temporary_url_generator' => 'flysystem.test.temporary_url_generator',
            ],
            'fs_read_only' => [
                'local' => [
                    'directory' => '/tmp/storage',
                ],
                'read_only' => true,
            ],
        ],
    ]);
};
