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

use League\Flysystem\Visibility;
use League\FlysystemBundle\Adapter\Builder\BunnyCDNAdapterDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter;

class BunnyCDNAdapterDefinitionBuilderTest extends TestCase
{
    public function createBuilder(): BunnyCDNAdapterDefinitionBuilder
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

    /**
     * @dataProvider provideValidOptions
     */
    public function testCreateDefinition(array $options): void
    {
        $this->assertSame(BunnyCDNAdapter::class, $this->createBuilder()->createDefinition($options, null)->getClass());
    }

    public function testOptionsBehavior(): void
    {
        $definition = $this->createBuilder()->createDefinition([
            'client' => 'bunny_client',
            'pull_zone' => 'z1',
        ], Visibility::PUBLIC);

        $this->assertSame(BunnyCDNAdapter::class, $definition->getClass());
        $this->assertSame('bunny_client', (string) $definition->getArgument(0));
        $this->assertSame('z1', $definition->getArgument(1));
    }
}
