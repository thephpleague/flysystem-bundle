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

use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\FlysystemBundle\Adapter\Builder\MemoryAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class MemoryAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder()
    {
        return new MemoryAdapterDefinitionBuilder();
    }

    public function testOptionsBehavior()
    {
        $this->assertSame(InMemoryFilesystemAdapter::class, $this->createBuilder()->createDefinition([])->getClass());
    }
}
