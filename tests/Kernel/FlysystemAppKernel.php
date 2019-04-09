<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle\Kernel;

use League\FlysystemBundle\FlysystemBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;

class FlysystemAppKernel extends Kernel
{
    use AppKernelTrait;

    private $adapterClients = [];

    public function registerBundles()
    {
        return [new FrameworkBundle(), new FlysystemBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $adapterClients = $this->adapterClients;

        $loader->load(function (ContainerBuilder $container) use ($adapterClients) {
            foreach ($adapterClients as $service => $mock) {
                $container->setDefinition($service, new Definition())->setSynthetic(true);
            }
        });

        $loader->load(__DIR__.'/config/framework.yaml', 'yaml');
        $loader->load(__DIR__.'/config/flysystem.yaml', 'yaml');
        $loader->load(__DIR__.'/config/services.yaml', 'yaml');
    }

    public function setAdapterClients(array $adapterClients)
    {
        $this->adapterClients = $adapterClients;
    }
}
