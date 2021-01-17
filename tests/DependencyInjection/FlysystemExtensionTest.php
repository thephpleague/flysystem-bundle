<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\DependencyInjection;

use AsyncAws\S3\S3Client as AsyncS3Client;
use Aws\S3\S3Client;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Tests\League\FlysystemBundle\Kernel\FlysystemAppKernel;

class FlysystemExtensionTest extends TestCase
{
    public function provideFilesystems()
    {
        $fsNames = [
            'fs_aws',
            'fs_custom',
            'fs_ftp',
            'fs_gcloud',
            'fs_lazy',
            'fs_local',
            'fs_sftp',
        ];

        foreach ($fsNames as $fsName) {
            yield $fsName => [$fsName];
        }
    }

    /**
     * @dataProvider provideFilesystems
     */
    public function testFileystems(string $fsName)
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        $fs = $container->get('flysystem.test.'.$fsName);

        $this->assertInstanceOf(FilesystemOperator::class, $fs, 'Filesystem "'.$fsName.'" should be an instance of FilesystemOperator');
    }

    /**
     * @dataProvider provideFilesystems
     */
    public function testTaggedCollection(string $fsName)
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        if (!$container->has('storages_tagged_collection')) {
            $this->markTestSkipped('Symfony 4.3+ is required to use indexed tagged service collections');
        }

        $storages = iterator_to_array($container->get('storages_tagged_collection')->locator);

        $this->assertInstanceOf(FilesystemOperator::class, $storages[$fsName]);
    }

    private function createFysystemKernel()
    {
        (new Dotenv())->populate([
            'AWS_BUCKET' => 'bucket-name',
            'LAZY_SOURCE' => 'fs_memory',
            'FTP_PORT' => 21,
        ]);

        $kernel = new FlysystemAppKernel('test', true);
        $kernel->setAdapterClients($this->getClientMocks());
        $kernel->boot();

        $container = $kernel->getContainer()->get('test.service_container');
        foreach ($this->getClientMocks() as $service => $mock) {
            if ($mock) {
                $container->set($service, $mock);
            }
        }

        return $kernel;
    }

    private function getClientMocks()
    {
        $gcloud = $this->createMock(StorageClient::class);
        $gcloud->method('bucket')->willReturn($this->createMock(Bucket::class));

        return [
            'aws_client_service' => $this->createMock(S3Client::class),
            'asyncaws_client_service' => $this->createMock(AsyncS3Client::class),
            'gcloud_client_service' => $gcloud,
        ];
    }
}
