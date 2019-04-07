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
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (!isset($config['filesystems'][$config['default_filesystem']])) {
            throw new \LogicException('Default filesystem "'.$config['default_filesystem'].'" is not defined in the "flysystem.filesystems" configuration key.');
        }

        $container->setParameter(
            'flysystem.default_local_directory',
            $config['default_local_directory'] ?: $container->getParameter('kernel.project_dir').'/storage'
        );

        foreach ($config['filesystems'] as $fsName => $fsConfig) {
            if (!$fsConfig['adapter'] && !$fsConfig['mounts']) {
                throw new \LogicException('Definition of the filesystem "'.$fsName.'" is invalid: one of the "adapter" and "mounts" keys is required.');
            }

            if ($fsConfig['adapter'] && $fsConfig['mounts']) {
                throw new \LogicException('Definition of the filesystem "'.$fsName.'" is invalid: configuring both "adapter" and "mounts" keys is not allowed.');
            }

            // Create adapter definition
            if ($fsConfig['cache']) {
                $adapterDefinition = new Definition(CachedAdapter::class);
                $adapterDefinition->setPublic(false);
                $adapterDefinition->setArgument(0, new Reference($fsConfig['adapter']));
                $adapterDefinition->setArgument(1, new Reference($fsConfig['cache']));

                $container->setDefinition('flysystem.filesystem.'.$fsName.'.adapter', $adapterDefinition);
            } else {
                $container->setAlias('flysystem.filesystem.'.$fsName.'.adapter', new Alias($fsConfig['adapter']));
            }

            $definition = new Definition(Filesystem::class);
            $definition->setPublic(false);
            $definition->setArgument(0, new Reference('flysystem.filesystem.'.$fsName.'.adapter'));
            $definition->setArgument(1, $fsConfig['config']);

            $container->setDefinition('flysystem.filesystem.'.$fsName, $definition);

            $container
                ->registerAliasForArgument('flysystem.filesystem.'.$fsName, FilesystemInterface::class, $fsName)
                ->setPublic(false);

            if ($config['default_filesystem'] === $fsName) {
                $container->setAlias(FilesystemInterface::class, 'flysystem.filesystem.'.$fsName)->setPublic(false);
                $container->setAlias('flysystem', 'flysystem.filesystem.'.$fsName)->setPublic(false);
            }
        }
    }
}
