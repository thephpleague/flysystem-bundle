<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\Command;

use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\League\FlysystemBundle\Kernel\FrameworkAppKernel;

class TransferCommandTest extends KernelTestCase
{
    private const BASE_DIR = 'flysystem-bundle-command-tests';

    private string $storageDirectory;
    private string $workingDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $base = sys_get_temp_dir().'/'.self::BASE_DIR;
        $this->removeDirectory($base);

        $this->storageDirectory = $base.'/storage';
        $this->workingDirectory = $base.'/work';

        mkdir($this->storageDirectory, 0755, true);
        mkdir($this->workingDirectory, 0755, true);
    }

    public function testPushCommandPushesALocalFileToTheConfiguredStorage(): void
    {
        file_put_contents($localFile = $this->workingDirectory.'/push.txt', 'push-content');

        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('flysystem:push');
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([
            'storage' => 'uploads.storage',
            'source' => $localFile,
        ]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertSame('push-content', file_get_contents($this->storageDirectory.'/push.txt'));
        self::assertStringContainsString('Pushed', $tester->getDisplay());
    }

    public function testPushCommandAsksForStorageSourceAndDestinationInteractively(): void
    {
        file_put_contents($localFile = $this->workingDirectory.'/interactive-push.txt', 'interactive-push-content');
        $destination = 'nested/interactive-result.txt';

        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('flysystem:push');
        $tester = new CommandTester($command);
        $tester->setInputs([
            'uploads.storage',
            $localFile,
            $destination,
        ]);

        $exitCode = $tester->execute([], ['interactive' => true]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertSame('interactive-push-content', file_get_contents($this->storageDirectory.'/'.$destination));
        self::assertStringContainsString('Which configured Flysystem storage should be used?', $tester->getDisplay());
        self::assertStringContainsString('What is the source path to transfer?', $tester->getDisplay());
        self::assertStringContainsString('What is the destination path?', $tester->getDisplay());
        self::assertStringContainsString('Pushed', $tester->getDisplay());
    }

    public function testPullCommandPullsAStorageFileToTheLocalFilesystem(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $container = static::getContainer();

        /** @var FilesystemOperator $storage */
        $storage = $container->get('uploads.storage');
        $storage->write('remote.txt', 'pull-content');

        $command = $application->find('flysystem:pull');
        $tester = new CommandTester($command);

        $destination = $this->workingDirectory.'/nested/local.txt';
        $exitCode = $tester->execute([
            'storage' => 'uploads.storage',
            'source' => 'remote.txt',
            'destination' => $destination,
        ]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertSame('pull-content', file_get_contents($destination));
        self::assertStringContainsString('Pulled', $tester->getDisplay());
    }

    public function testPullCommandFailsWhenDestinationAlreadyExists(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $container = static::getContainer();

        /** @var FilesystemOperator $storage */
        $storage = $container->get('uploads.storage');
        $storage->write('remote.txt', 'pull-content');

        $destination = $this->workingDirectory.'/local.txt';
        file_put_contents($destination, 'existing-content');

        $command = $application->find('flysystem:pull');
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([
            'storage' => 'uploads.storage',
            'source' => 'remote.txt',
            'destination' => $destination,
        ]);

        self::assertSame(Command::FAILURE, $exitCode);
        self::assertSame('existing-content', file_get_contents($destination));
        self::assertStringContainsString('already exists', $tester->getDisplay());
    }

    public function testPullCommandOverwritesWhenDestinationAlreadyExistsWithForce(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $container = static::getContainer();

        /** @var FilesystemOperator $storage */
        $storage = $container->get('uploads.storage');
        $storage->write('remote.txt', 'pull-content');

        $destination = $this->workingDirectory.'/local.txt';
        file_put_contents($destination, 'existing-content');

        $command = $application->find('flysystem:pull');
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([
            'storage' => 'uploads.storage',
            'source' => 'remote.txt',
            'destination' => $destination,
            '--force' => true,
        ]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertSame('pull-content', file_get_contents($destination));
        self::assertStringContainsString('Pulled', $tester->getDisplay());
    }

    public function testPushCommandFailsForUnknownStorage(): void
    {
        file_put_contents($localFile = $this->workingDirectory.'/push.txt', 'push-content');

        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('flysystem:push');
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([
            'storage' => 'unknown.storage',
            'source' => $localFile,
        ]);

        self::assertSame(Command::INVALID, $exitCode);
        self::assertStringContainsString('The storage "unknown.storage" does not exist.', $tester->getDisplay());
    }

    protected static function createKernel(array $options = []): FrameworkAppKernel
    {
        return new FrameworkAppKernel('test', true, self::getStorageDirectory());
    }

    private static function getStorageDirectory(): string
    {
        $storageDirectory = sys_get_temp_dir().'/'.self::BASE_DIR.'/storage';

        if (!is_dir($storageDirectory)) {
            mkdir($storageDirectory, 0755, true);
        }

        return $storageDirectory;
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($directory);
    }
}
