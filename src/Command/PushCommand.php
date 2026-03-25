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

#[AsCommand(name: 'flysystem:push', description: 'Push a local file to a configured Flysystem storage.')]
final class PushCommand extends AbstractTransferCommand
{
    protected function transfer(FilesystemOperator $storage, string $source, string $destination, bool $force = false): void
    {
        if (!is_file($source)) {
            throw new \InvalidArgumentException(sprintf('The source file "%s" does not exist or is not a regular file.', $source));
        }

        if (!$force && $storage->fileExists($destination)) {
            throw new \RuntimeException(sprintf('The destination file "%s" already exists on the storage. Use the --force option to overwrite it.', $destination));
        }

        $resource = fopen($source, 'rb');
        if (false === $resource) {
            throw new \RuntimeException(sprintf('Unable to open the source file "%s" for reading.', $source));
        }

        try {
            $storage->writeStream($destination, $resource);
        } finally {
            fclose($resource);
        }
    }

    protected function createSuccessMessage(string $storageName, string $source, string $destination): string
    {
        return sprintf('Pushed "%s" to "%s" on storage "%s".', $source, $destination, $storageName);
    }
}
