<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle;

use League\FlysystemBundle\FlysystemBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class FlysystemBundleTest extends TestCase
{
    public function provideKernels()
    {
        yield 'empty' => [new EmptyAppKernel('test', true)];
        yield 'framework' => [new FrameworkAppKernel('test', true)];
    }

    /**
     * @dataProvider provideKernels
     */
    public function testBootKernel(Kernel $kernel)
    {
        $kernel->boot();

        $this->assertArrayHasKey('FlysystemBundle', $kernel->getBundles());
    }
}

class EmptyAppKernel extends Kernel
{
    use AppKernelTestTrait;

    public function registerBundles()
    {
        return [new FlysystemBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function ($container) {
            $container->loadFromExtension('flysystem', [
                'default_filesystem' => 'app',
                'filesystems' => ['app' => ['adapter' => 'flysystem.adapter.local']],
            ]);
        });
    }
}

class FrameworkAppKernel extends Kernel
{
    use AppKernelTestTrait;

    public function registerBundles()
    {
        return [new FrameworkBundle(), new FlysystemBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function ($container) {
            $container->loadFromExtension('framework', ['secret' => '$ecret']);

            $container->loadFromExtension('flysystem', [
                'default_filesystem' => 'app',
                'filesystems' => ['app' => ['adapter' => 'flysystem.adapter.local']],
            ]);
        });
    }
}
