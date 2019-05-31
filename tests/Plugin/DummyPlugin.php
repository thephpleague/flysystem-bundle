<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\Plugin;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;

class DummyPlugin implements PluginInterface
{
    public function handle()
    {
        return 'plugin';
    }

    public function getMethod()
    {
        return 'pluginTest';
    }

    public function setFilesystem(FilesystemInterface $filesystem)
    {
    }
}
