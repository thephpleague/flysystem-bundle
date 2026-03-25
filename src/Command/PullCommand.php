<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\Command;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'flysystem:pull', description: 'Pull a file from a configured Flysystem storage to the local filesystem.')]
final class PullCommand extends AbstractTransferCommand
{
    protected function normalizeDestination(string $source, string $destination): string
    {
        if (!is_dir($destination)) {
            return $destination;
        }

        return rtrim($destination, '/\\').DIRECTORY_SEPARATOR.basename($source);
    }

    protected function transfer(FilesystemOperator $storage, string $source, string $destination, bool $force = false): void
    {
        $directory = dirname($destination);
        if ('.' !== $directory && !is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Unable to create the destination directory "%s".', $directory));
        }

        if (!$force && is_file($destination)) {
            throw new \RuntimeException(sprintf('The destination file "%s" already exists. Use the --force option to overwrite it.', $destination));
        }

        $resource = $storage->readStream($source);
        if (!is_resource($resource)) {
            throw new \RuntimeException(sprintf('Unable to read the source file "%s" from storage.', $source));
        }

        $local = fopen($destination, 'wb');
        if (false === $local) {
            if (is_resource($resource)) {
                fclose($resource);
            }

            throw new \RuntimeException(sprintf('Unable to open the destination file "%s" for writing.', $destination));
        }

        try {
            $bytesCopied = stream_copy_to_stream($resource, $local);
            if (false === $bytesCopied) {
                throw new \RuntimeException(sprintf('Failed to write "%s" to "%s": stream copy failed.', $source, $destination));
            }
        } finally {
            fclose($resource);
            fclose($local);
        }
    }

    protected function createSuccessMessage(string $storageName, string $source, string $destination): string
    {
        return sprintf('Pulled "%s" from storage "%s" to "%s".', $source, $storageName, $destination);
    }
}
