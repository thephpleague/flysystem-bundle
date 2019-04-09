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

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\FlysystemBundle\Adapter\AdapterDefinitionFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

        $this->registerFilesystems($container, $config['filesystems']);

        // Create default filesystem alias
        if (!isset($config['filesystems'][$config['default_filesystem']])) {
            throw new \LogicException('Default filesystem "'.$config['default_filesystem'].'" is not defined in the "flysystem.filesystems" configuration key.');
        }

        $container->setAlias(FilesystemInterface::class, $config['default_filesystem'])->setPublic(false);
        $container->setAlias('flysystem', $config['default_filesystem'])->setPublic(false);
    }

    private function registerFilesystems(ContainerBuilder $container, array $filesystems)
    {
        $adapterFactory = new AdapterDefinitionFactory();

        foreach ($filesystems as $fsName => $fsConfig) {
            // Create adapter service definition
            if ($adapter = $adapterFactory->createDefinition($fsConfig['adapter'], $fsConfig['options'])) {
                // Native adapter
                $container->setDefinition('flysystem.adapter.'.$fsName, $adapter)->setPublic(false);
            } else {
                // Custom adapter
                $container->setAlias('flysystem.adapter.'.$fsName, $fsConfig['adapter'])->setPublic(false);
            }

            // Create filesystem service definition
            $definition = $this->createFilesystemDefinition(new Reference('flysystem.adapter.'.$fsName), $fsConfig);

            $container->setDefinition($fsName, $definition);
            $container->registerAliasForArgument($fsName, FilesystemInterface::class, $fsName)->setPublic(false);
        }
    }

    private function createFilesystemDefinition(Reference $adapter, array $config)
    {
        $definition = new Definition(Filesystem::class);
        $definition->setPublic(false);
        $definition->setArgument(0, $adapter);
        $definition->setArgument(1, [
            'visibility' => $config['visibility'],
            'case_sensitive' => $config['case_sensitive'],
            'disable_asserts' => $config['disable_asserts'],
        ]);

        return $definition;
    }
}
