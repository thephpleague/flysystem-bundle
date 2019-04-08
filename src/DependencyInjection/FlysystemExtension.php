<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\DependencyInjection;

use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Cached\Storage\Psr6Cache;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\FlysystemBundle\Adapter\AdapterDefinitionFactory;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @final
 */
class FlysystemExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerCacheProviders($container);
        $this->registerFilesystems($container, $config['filesystems']);

        // Create default filesystem alias
        if (!isset($config['filesystems'][$config['default_filesystem']])) {
            throw new \LogicException('Default filesystem "'.$config['default_filesystem'].'" is not defined in the "flysystem.filesystems" configuration key.');
        }

        $defaultFsName = $config['default_filesystem'];
        $container->setAlias(FilesystemInterface::class, 'flysystem.filesystem.'.$defaultFsName)->setPublic(false);
        $container->setAlias('flysystem', 'flysystem.filesystem.'.$defaultFsName)->setPublic(false);
    }

    private function registerCacheProviders(ContainerBuilder $container)
    {
        $container->setDefinition(
            'flysystem.cache.memory',
            (new Definition(Memory::class))
                ->setPrivate(true)
        );

        $container->setDefinition(
            'flysystem.cache.app',
            (new Definition(Psr6Cache::class))
                ->setPrivate(true)
                ->setArgument(0, new Reference('cache.app', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE))
        );
    }

    private function registerFilesystems(ContainerBuilder $container, array $filesystems)
    {
        $definitionFactory = new AdapterDefinitionFactory();

        foreach ($filesystems as $fsName => $fsConfig) {
            if ($fsConfig['adapter'] && $fsConfig['mounts']) {
                throw new \LogicException('Definition of the filesystem "'.$fsName.'" is invalid: configuring both "adapter" and "mounts" keys is not allowed.');
            }

            if ($fsConfig['adapter']) {
                $adapterDefinition = $definitionFactory->createDefinition($fsConfig['adapter'], $fsConfig['options']);
            } elseif ($fsConfig['mounts']) {
                $adapterDefinition = $definitionFactory->createDefinition('mount', $fsConfig['mounts']);
            } else {
                throw new \LogicException('Definition of the filesystem "'.$fsName.'" is invalid: one of the "adapter" and "mounts" keys is required.');
            }
        }
    }
}
