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

use League\FlysystemBundle\Adapter\AdapterDefinitionFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractAdapterDefinitionBuilderTest extends TestCase
{
    protected function createDefinitionFactory()
    {
        return $this->createMock(AdapterDefinitionFactory::class);
    }
}
