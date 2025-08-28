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

use League\FlysystemBundle\Adapter\Builder\BunnyCDNAdapterDefinitionBuilder;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter;
use Symfony\Component\DependencyInjection\Definition;

class BunnyCDNAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): BunnyCDNAdapterDefinitionBuilder
    {
        return new BunnyCDNAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'client' => 'bunny_client',
        ]];

        yield 'full' => [[
            'client' => 'bunny_client',
            'pull_zone' => 'z1',
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $this->assertSame(BunnyCDNAdapter::class, $definition->getClass());
        $this->assertSame('bunny_client', (string) $definition->getArgument(0));
        $this->assertSame('z1', $definition->getArgument(1));
    }
}
