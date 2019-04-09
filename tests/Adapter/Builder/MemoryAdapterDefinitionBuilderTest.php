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

use League\Flysystem\Memory\MemoryAdapter;
use League\FlysystemBundle\Adapter\Builder\MemoryAdapterDefinitionBuilder;

class MemoryAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    public function createBuilder()
    {
        return new MemoryAdapterDefinitionBuilder($this->createDefinitionFactory());
    }

    public function testOptionsBehavior()
    {
        $this->assertSame(MemoryAdapter::class, $this->createBuilder()->createDefinition([])->getClass());
    }
}
