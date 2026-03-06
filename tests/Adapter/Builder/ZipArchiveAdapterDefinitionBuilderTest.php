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

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use League\FlysystemBundle\Adapter\Builder\ZipArchiveAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class ZipArchiveAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): ZipArchiveAdapterDefinitionBuilder
    {
        return new ZipArchiveAdapterDefinitionBuilder();
    }

    public function testOptionsBehavior(): void
    {
        $this->assertSame(ZipArchiveAdapter::class, $this->createBuilder()->createDefinition([], null)->getClass());
    }
}
