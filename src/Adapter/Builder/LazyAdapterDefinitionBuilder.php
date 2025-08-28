<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\Adapter\Builder;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class LazyAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'lazy';
    }

    public function getRequiredPackages(): array
    {
        return [];
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->info('Lazy adapter for runtime storage selection')
            ->children()
                ->scalarNode('source')
                    ->info('The service name of the storage to use at runtime')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories = null): ?string
    {
        // For lazy adapter, we don't create a standard adapter
        // Instead, we create the storage definition directly here and return null
        // to indicate that no adapter service should be created

        $definition = new Definition(FilesystemOperator::class);
        $definition->setPublic(false);
        $definition->setFactory([new Reference('flysystem.adapter.lazy.factory'), 'createStorage']);
        $definition->setArgument(0, $options['source']);
        $definition->setArgument(1, $storageName);
        $definition->addTag('flysystem.storage', ['storage' => $storageName]);

        $container->setDefinition($storageName, $definition);

        // Return null to indicate this is handled specially
        return null;
    }
}
