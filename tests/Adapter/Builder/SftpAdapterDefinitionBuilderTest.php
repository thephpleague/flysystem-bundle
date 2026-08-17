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
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SftpAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): SftpAdapterDefinitionBuilder
    {
        return new SftpAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'host' => 'ftp.example.com',
            'username' => 'username',
        ]];

        yield 'full' => [[
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'passphrase' => null,
            'port' => 22,
            'useAgent' => false,
            'timeout' => 30,
            'maxTries' => 2,
            'hostFingerprint' => ['aa:bb:cc:dd', 'ee:ff:00:11'],
            'connectivityChecker' => 'my_service_check',
            'preferredAlgorithms' => [
                'hostkey' => ['rsa-sha2-256', 'ssh-rsa'],
            ],
            'root' => '/path/to/root',
            'mimeTypeDetector' => 'my_mime_type_detector',
            'detect_mime_type_using_path' => true,
            'disconnect_on_destruct' => true,
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $expected = [
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            'privateKey' => '/path/to/or/contents/of/privatekey',
            'passphrase' => null,
            'port' => 22,
            'useAgent' => false,
            'timeout' => 30,
            'maxTries' => 2,
            'hostFingerprint' => ['aa:bb:cc:dd', 'ee:ff:00:11'],
            'preferredAlgorithms' => [
                'hostkey' => ['rsa-sha2-256', 'ssh-rsa'],
            ],
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
        ];

        $this->assertSame(SftpAdapter::class, $definition->getClass());
        $connectionProviderOptions = $definition->getArgument(0)->getArgument(0);
        unset($connectionProviderOptions['connectivityChecker']);
        $this->assertSame($expected, $connectionProviderOptions);
        $this->assertSame('/path/to/root', $definition->getArgument(1));
        $this->assertSame(Visibility::PRIVATE, $definition->getArgument(2)->getArgument(1));
        $this->assertInstanceOf(Reference::class, $definition->getArgument(3));
        $this->assertSame('my_mime_type_detector', (string) $definition->getArgument(3));
        $this->assertTrue($definition->getArgument(4));
        $this->assertTrue($definition->getArgument(5));
    }
}
