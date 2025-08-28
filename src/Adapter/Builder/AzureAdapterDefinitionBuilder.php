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

use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class AzureAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'azure';
    }

    public function getRequiredPackages(): array
    {
        return [
            AzureBlobStorageAdapter::class => 'league/flysystem-azure-blob-storage',
        ];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setRequired('container');
        $resolver->setAllowedTypes('container', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('client')
                    ->isRequired()
                    ->info('The Azure Blob Storage client service name')
                ->end()
                ->scalarNode('container')
                    ->isRequired()
                    ->info('The name of the Azure Blob Storage container')
                ->end()
                ->scalarNode('prefix')
                    ->defaultValue('')
                    ->info('Optional path prefix to prepend to all blob names')
                ->end()
            ->end()
        ;
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        $container
            ->setDefinition($adapterId, new Definition(AzureBlobStorageAdapter::class))
            ->setArgument(0, new Reference($options['client']))
            ->setArgument(1, $options['container'])
            ->setArgument(2, $options['prefix']);

        return $adapterId;
    }
}
