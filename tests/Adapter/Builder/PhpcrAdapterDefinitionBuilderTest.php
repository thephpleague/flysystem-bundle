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

use League\Flysystem\Phpcr\PhpcrAdapter;
use League\FlysystemBundle\Adapter\Builder\PhpcrAdapterDefinitionBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PhpcrAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    public function createBuilder()
    {
        return new PhpcrAdapterDefinitionBuilder($this->createDefinitionFactory());
    }

    public function testOptionsBehavior()
    {
        $definition = $this->createBuilder()->createDefinition([
            'session' => 'my_session',
            'root' => '/my/root',
        ]);

        $this->assertSame(PhpcrAdapter::class, $definition->getClass());
        $this->assertInstanceOf(Reference::class, $definition->getArgument(0));
        $this->assertSame('my_session', (string) $definition->getArgument(0));
        $this->assertSame('/my/root', $definition->getArgument(1));
    }
}
