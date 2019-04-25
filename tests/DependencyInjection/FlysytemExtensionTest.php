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

class FlysytemExtensionTest extends TestCase
{
    public function testCreateFileystems()
    {
        (new Dotenv())->populate([
            'AWS_BUCKET' => 'bucket-name',
            'FTP_PORT' => 21,
        ]);

        $kernel = new FlysystemAppKernel('test', true);
        $kernel->setAdapterClients($this->getClientMocks());
        $kernel->boot();

        $container = $kernel->getContainer();
        foreach ($this->getClientMocks() as $service => $mock) {
            $container->set($service, $mock);
        }

        foreach ($this->getFilesystems() as $fsName) {
            $fs = $container->get('flysystem.test.'.$fsName);
            $this->assertInstanceOf(FilesystemInterface::class, $fs, 'Filesystem "'.$fsName.'" should be an instance of FilesystemInterface');
        }
    }

    private function getClientMocks()
    {
        $gcloud = $this->createMock(StorageClient::class);
        $gcloud->method('bucket')->willReturn($this->createMock(Bucket::class));

        return [
            'aws_client_service' => $this->createMock(S3Client::class),
            'azure_client_service' => $this->createMock(BlobRestProxy::class),
            'dropbox_client_service' => $this->createMock(DropboxClient::class),
            'gcloud_client_service' => $gcloud,
            'rackspace_container_service' => $this->createMock(Container::class),
            'webdav_client_service' => $this->createMock(WebDAVClient::class),
        ];
    }

    private function getFilesystems()
    {
        return [
            'fs_aws',
            'fs_azure',
            'fs_cache',
            'fs_custom',
            'fs_dropbox',
            'fs_ftp',
            'fs_gcloud',
            'fs_local',
            'fs_rackspace',
            'fs_replicate',
            'fs_sftp',
            'fs_webdav',
            'fs_zip',
        ];
    }
}
