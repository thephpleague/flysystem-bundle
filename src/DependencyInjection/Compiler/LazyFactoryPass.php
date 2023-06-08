<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class LazyFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $factories = [];
        foreach ($container->findTaggedServiceIds('flysystem.storage') as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['storage'])) {
                    $factories[$tag['storage']] = new Reference($serviceId);
                }
            }
        }

        $lazyFactory = $container->getDefinition('flysystem.adapter.lazy.factory');
        $lazyFactory->setArgument(0, ServiceLocatorTagPass::register($container, $factories));
    }
}
