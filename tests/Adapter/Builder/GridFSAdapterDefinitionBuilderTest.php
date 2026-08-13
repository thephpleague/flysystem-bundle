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

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use League\Flysystem\GridFS\GridFSAdapter;
use League\FlysystemBundle\Adapter\Builder\GridFSAdapterDefinitionBuilder;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use MongoDB\Client;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class GridFSAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): GridFSAdapterDefinitionBuilder
    {
        return new GridFSAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'doctrine_minimal' => [[
            'doctrine_connection' => 'default',
        ]];

        yield 'doctrine_full' => [[
            'doctrine_connection' => 'custom',
            'database' => 'testing',
            'bucket' => 'avatars',
        ]];

        yield 'minimal' => [[
            'mongodb_uri' => 'mongodb://localhost:27017/',
            'database' => 'testing',
        ]];

        yield 'full' => [[
            'mongodb_uri' => 'mongodb://server1:27017,server2:27017/',
            'mongodb_uri_options' => ['appname' => 'flysystem'],
            'mongodb_driver_options' => ['disableClientPersistence' => false],
            'database' => 'testing',
            'bucket' => 'avatars',
            'prefix' => 'prefix/path',
            'mimeTypeDetector' => 'my_mime_type_detector',
        ]];

        yield 'service' => [[
            'bucket' => 'bucket',
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $this->assertSame(GridFSAdapter::class, $definition->getClass());

        $bucketDefinition = $definition->getArgument(0);
        $this->assertInstanceOf(Definition::class, $bucketDefinition);
        $this->assertSame('mongodb://server1:27017,server2:27017/', $bucketDefinition->getArgument(0));
        $this->assertSame(['appname' => 'flysystem'], $bucketDefinition->getArgument(1));
        $this->assertSame(['disableClientPersistence' => false], $bucketDefinition->getArgument(2));
        $this->assertSame('testing', $bucketDefinition->getArgument(3));
        $this->assertSame('avatars', $bucketDefinition->getArgument(4));

        $this->assertSame('prefix/path', $definition->getArgument(1));

        $this->assertInstanceOf(Reference::class, $definition->getArgument(2));
        $this->assertSame('my_mime_type_detector', (string) $definition->getArgument(2));
    }

    public static function provideInvalidOptions(): \Generator
    {
        yield 'empty' => [
            [],
            'Flysystem GridFS configuration requires a "bucket" service name, a "mongodb_uri" or a "doctrine_connection" name',
        ];

        yield 'no database with mongodb_uri' => [
            ['mongodb_uri' => 'mongodb://127.0.0.1:27017/'],
            'MongoDB "database" name is required for Flysystem GridFS configuration',
        ];

        yield 'both doctrine_connection and mongodb_uri' => [
            ['doctrine_connection' => 'default', 'mongodb_uri' => 'mongodb://127.0.0.1:27017/'],
            'In GridFS configuration, "doctrine_connection" and "mongodb_uri" options cannot be set together.',
        ];
    }

    /**
     * @dataProvider provideInvalidOptions
     */
    public function testInvalidOptions(array $options, string $message): void
    {
        $builder = $this->createBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $builder->createAdapter($this->getContainer(), 'test_storage', $options, null);
    }

    public function testInitializeBucketFromDocumentManager(): void
    {
        if (!class_exists(DocumentManager::class)) {
            self::markTestSkipped('Doctrine ODM is not installed, skipping test.');
        }

        $client = new Client();
        $config = new Configuration();
        $config->setDefaultDB('testing');
        $dm = $this->createMock(DocumentManager::class);
        $dm->expects($this->once())->method('getClient')->willReturn($client);
        $dm->expects($this->once())->method('getConfiguration')->willReturn($config);

        $bucket = GridFSAdapterDefinitionBuilder::initializeBucketFromDocumentManager($dm, null, 'avatars');

        $this->assertSame('testing', $bucket->getDatabaseName());
        $this->assertSame('avatars', $bucket->getBucketName());
    }

    public function testInitializeBucketFromConfig(): void
    {
        $bucket = GridFSAdapterDefinitionBuilder::initializeBucketFromConfig(
            'mongodb://server:27017/',
            ['appname' => 'flysystem'],
            ['disableClientPersistence' => false],
            'testing',
            'avatars'
        );

        $this->assertSame('testing', $bucket->getDatabaseName());
        $this->assertSame('avatars', $bucket->getBucketName());
    }
}
