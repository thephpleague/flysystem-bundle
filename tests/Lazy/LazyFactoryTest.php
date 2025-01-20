<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\Lazy;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\FlysystemBundle\Lazy\LazyFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LazyFactoryTest extends TestCase
{
    public function testItPreventsInfiniteRecursion(): void
    {
        $containerBuilder = new ContainerBuilder();
        $factory = new LazyFactory($containerBuilder);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "lazy" adapter source is referring to itself as "lazy_storage", which would lead to infinite recursion.');
        $factory->createStorage('lazy_storage', 'lazy_storage');
    }

    public function testItErrorsWhenServiceIsNotAvailable(): void
    {
        $containerBuilder = new ContainerBuilder();
        $factory = new LazyFactory($containerBuilder);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You have requested a non-existent source storage "source_storage" in lazy storage "lazy_storage".');
        $factory->createStorage('source_storage', 'lazy_storage');
    }

    public function testItReturnsTheRequestedStorageService(): void
    {
        $actual = new Filesystem(new InMemoryFilesystemAdapter());
        $lazy = new Filesystem(new InMemoryFilesystemAdapter());

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->set('source_storage', $actual);
        $containerBuilder->set('lazy_storage', $lazy);

        $factory = new LazyFactory($containerBuilder);

        $adapter = $factory->createStorage('source_storage', 'lazy_storage');
        self::assertSame($actual, $adapter);
    }
}
