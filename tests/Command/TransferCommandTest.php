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

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\League\FlysystemBundle\Kernel\FrameworkAppKernel;

class TransferCommandTest extends KernelTestCase
{
    private string $storageDirectory;
    private string $workingDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $base = sys_get_temp_dir().'/flysystem-bundle-command-tests';
        $this->removeDirectory($base);

        $this->storageDirectory = $base.'/storage';
        $this->workingDirectory = $base.'/work';

        mkdir($this->storageDirectory, 0777, true);
        mkdir($this->workingDirectory, 0777, true);
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

        self::assertSame(0, $exitCode);
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

        self::assertSame(0, $exitCode);
        self::assertSame('interactive-push-content', file_get_contents($this->storageDirectory.'/'.$destination));
        self::assertStringContainsString('Which configured Flysystem storage should be used?', $tester->getDisplay());
        self::assertStringContainsString('What is the source path to transfer?', $tester->getDisplay());
        self::assertStringContainsString('What is the destination path?', $tester->getDisplay());
        self::assertStringContainsString('Pushed', $tester->getDisplay());
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

        self::assertSame(2, $exitCode);
        self::assertStringContainsString('The storage "unknown.storage" does not exist.', $tester->getDisplay());
    }

    protected static function createKernel(array $options = []): FrameworkAppKernel
    {
        return new FrameworkAppKernel('test', true, self::getStorageDirectory());
    }

    private static function getStorageDirectory(): string
    {
        $storageDirectory = sys_get_temp_dir().'/flysystem-bundle-command-tests/storage';

        if (!is_dir($storageDirectory)) {
            mkdir($storageDirectory, 0777, true);
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
