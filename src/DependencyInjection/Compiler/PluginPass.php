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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author BoShurik <boshurik@gmail.com>
 *
 * @internal
 */
class PluginPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $plugins = array_map(function ($id) {
            return new Reference($id);
        }, array_keys($container->findTaggedServiceIds('flysystem.plugin')));

        if (0 === count($plugins)) {
            return;
        }

        /** @var Definition[] $storages */
        $storages = array_map(function ($id) use ($container) {
            return $container->findDefinition($id);
        }, array_keys($container->findTaggedServiceIds('flysystem.storage')));

        foreach ($storages as $storage) {
            foreach ($plugins as $plugin) {
                $storage->addMethodCall('addPlugin', [$plugin]);
            }
        }
    }
}
