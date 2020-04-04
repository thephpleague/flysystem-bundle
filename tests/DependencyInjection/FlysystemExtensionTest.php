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
use League\Flysystem\FilesystemInterface;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use OpenCloud\ObjectStore\Resource\Container;
use PHPUnit\Framework\TestCase;
use Sabre\DAV\Client as WebDAVClient;
use Spatie\Dropbox\Client as DropboxClient;
use Symfony\Component\Dotenv\Dotenv;
use Tests\League\FlysystemBundle\Kernel\FlysystemAppKernel;

class FlysystemExtensionTest extends TestCase
{
    public function provideFilesystems()
    {
        $fsNames = [
            'fs_asyncaws',
            'fs_aws',
            'fs_azure',
            'fs_cache',
            'fs_custom',
            'fs_dropbox',
            'fs_ftp',
            'fs_gcloud',
            'fs_lazy',
            'fs_local',
            'fs_rackspace',
            'fs_replicate',
            'fs_sftp',
            'fs_webdav',
            'fs_zip',
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
        $fs = $kernel->getContainer()->get('flysystem.test.'.$fsName);

        $this->assertInstanceOf(FilesystemInterface::class, $fs, 'Filesystem "'.$fsName.'" should be an instance of FilesystemInterface');
        $this->assertEquals('plugin', $fs->pluginTest());
    }

    /**
     * @dataProvider provideFilesystems
     */
    public function testTaggedCollection(string $fsName)
    {
        $kernel = $this->createFysystemKernel();

        if (!$kernel->getContainer()->has('storages_tagged_collection')) {
            $this->markTestSkipped('Symfony 4.3+ is required to use indexed tagged service collections');
        }

        $storages = iterator_to_array($kernel->getContainer()->get('storages_tagged_collection')->locator);

        $this->assertInstanceOf(FilesystemInterface::class, $storages[$fsName]);
        $this->assertEquals('plugin', $storages[$fsName]->pluginTest());
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

        $container = $kernel->getContainer();
        foreach ($this->getClientMocks() as $service => $mock) {
            $container->set($service, $mock);
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
            'azure_client_service' => $this->createMock(BlobRestProxy::class),
            'dropbox_client_service' => $this->createMock(DropboxClient::class),
            'gcloud_client_service' => $gcloud,
            'rackspace_container_service' => $this->createMock(Container::class),
            'webdav_client_service' => $this->createMock(WebDAVClient::class),
        ];
    }
}
