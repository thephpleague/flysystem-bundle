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
use MongoDB\Client;
use MongoDB\GridFS\Bucket;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class GridFSAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): GridFSAdapterDefinitionBuilder
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

        yield 'config_minimal' => [[
            'mongodb_uri' => 'mongodb://localhost:27017/',
            'database' => 'testing',
        ]];

        yield 'config_full' => [[
            'mongodb_uri' => 'mongodb://server1:27017,server2:27017/',
            'mongodb_uri_options' => ['appname' => 'flysystem'],
            'mongodb_driver_options' => ['disableClientPersistence' => false],
            'database' => 'testing',
            'bucket' => 'avatars',
        ]];

        yield 'service' => [[
            'bucket' => 'bucket',
        ]];
    }

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition($options): void
    {
        $this->assertSame(GridFSAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
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

        $builder->createDefinition($options, null);
    }

    public function testInitializeBucketFromDocumentManager(): void
    {
        $client = new Client();
        $config = new Configuration();
        $config->setDefaultDB('testing');
        $dm = $this->createMock(DocumentManager::class);
        $dm->expects($this->once())->method('getClient')->willReturn($client);
        $dm->expects($this->once())->method('getConfiguration')->willReturn($config);

        $bucket = GridFSAdapterDefinitionBuilder::initializeBucketFromDocumentManager($dm, null, 'avatars');

        $this->assertInstanceOf(Bucket::class, $bucket);
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

        $this->assertInstanceOf(Bucket::class, $bucket);
        $this->assertSame('testing', $bucket->getDatabaseName());
        $this->assertSame('avatars', $bucket->getBucketName());
    }
}
