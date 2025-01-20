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
use League\Flysystem\UnableToWriteFile;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Tests\League\FlysystemBundle\Kernel\FlysystemAppKernel;

class FlysystemExtensionTest extends TestCase
{
    public function provideFilesystems(): \Generator
    {
        $fsNames = [
            'fs_aws',
            'fs_azure',
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
    public function testFilesystems(string $fsName): void
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        $fs = $container->get('flysystem.test.'.$fsName);

        $this->assertInstanceOf(FilesystemOperator::class, $fs, 'Filesystem "'.$fsName.'" should be an instance of FilesystemOperator');
    }

    /**
     * @dataProvider provideFilesystems
     */
    public function testTaggedCollection(string $fsName): void
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        if (!$container->has('storages_tagged_collection')) {
            $this->markTestSkipped('Symfony 4.3+ is required to use indexed tagged service collections');
        }

        $storages = iterator_to_array($container->get('storages_tagged_collection')->locator);

        $this->assertInstanceOf(FilesystemOperator::class, $storages[$fsName]);
    }

    public function testPublicUrl(): void
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        $fs = $container->get('flysystem.test.fs_public_url');

        self::assertSame('https://example.org/assets/test1.txt', $fs->publicUrl('test1.txt'));
    }

    public function testPublicUrls(): void
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        $fs = $container->get('flysystem.test.fs_public_urls');

        self::assertSame('https://cdn1.example.org/test1.txt', $fs->publicUrl('test1.txt'));
        self::assertSame('https://cdn2.example.org/yo/test2.txt', $fs->publicUrl('yo/test2.txt'));
        self::assertSame('https://cdn3.example.org/yww/test1.txt', $fs->publicUrl('yww/test1.txt'));
    }

    public function testUrlGenerators(): void
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        $fs = $container->get('flysystem.test.fs_url_generator');

        self::assertSame('https://example.org/generator/test1.txt', $fs->publicUrl('test1.txt'));
        self::assertSame('https://example.org/temporary/test1.txt?expiresAt=1670846026', $fs->temporaryUrl('test1.txt', new \DateTimeImmutable('@1670846026')));
    }

    public function testReadOnly(): void
    {
        $kernel = $this->createFysystemKernel();
        $container = $kernel->getContainer()->get('test.service_container');

        $fs = $container->get('flysystem.test.fs_read_only');

        $this->expectException(UnableToWriteFile::class);
        $this->expectExceptionMessage('Unable to write file at location: path/to/file. This is a readonly adapter.');

        $fs->write('/path/to/file', 'Unable to write in read only');
    }

    private function createFysystemKernel(): FlysystemAppKernel
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

    private function getClientMocks(): array
    {
        $gcloud = $this->createMock(StorageClient::class);
        $gcloud->method('bucket')->willReturn($this->createMock(Bucket::class));

        return [
            'aws_client_service' => $this->createMock(S3Client::class),
            'asyncaws_client_service' => $this->createMock(AsyncS3Client::class),
            'azure_client_service' => $this->createMock(BlobRestProxy::class),
            'gcloud_client_service' => $gcloud,
        ];
    }
}
