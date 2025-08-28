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

use Google\Cloud\Storage\StorageClient;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler;
use League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility;
use League\Flysystem\Visibility;
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
final class GcloudAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'gcloud';
    }

    public function getRequiredPackages(): array
    {
        return [
            GoogleCloudStorageAdapter::class => 'league/flysystem-google-cloud-storage',
        ];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setRequired('bucket');
        $resolver->setAllowedTypes('bucket', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setDefault('visibility_handler', null);
        $resolver->setAllowedTypes('visibility_handler', ['string', 'null']);

        $resolver->setDefault('streamReads', false);
        $resolver->setAllowedTypes('streamReads', 'bool');
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('client')
                    ->isRequired()
                    ->info('The Google Cloud Storage client service name')
                ->end()
                ->scalarNode('bucket')
                    ->isRequired()
                    ->info('The name of the Google Cloud Storage bucket')
                ->end()
                ->scalarNode('prefix')
                    ->defaultValue('')
                    ->info('Optional path prefix to prepend to all object keys')
                ->end()
                ->scalarNode('visibility_handler')
                    ->defaultNull()
                    ->info('Optional visibility handler service name')
                ->end()
                ->booleanNode('streamReads')
                    ->defaultFalse()
                    ->info('Whether to use streaming for file reads')
                ->end()
            ->end()
        ;
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        // Register visibility handlers and their aliases
        $container->register(PortableVisibilityHandler::class, PortableVisibilityHandler::class);
        $container->setAlias('flysystem.adapter.gcloud.visibility.portable', PortableVisibilityHandler::class);

        $container->register(UniformBucketLevelAccessVisibility::class, UniformBucketLevelAccessVisibility::class);
        $container->setAlias('flysystem.adapter.gcloud.visibility.uniform', UniformBucketLevelAccessVisibility::class);

        $visibilityHandlerReference = null;
        if (null !== $options['visibility_handler']) {
            $visibilityHandlerReference = new Reference($options['visibility_handler']);
        }

        // Create the adapter
        $container
            ->setDefinition($adapterId, new Definition(GoogleCloudStorageAdapter::class))
            ->setArgument(0,
                (new Definition(StorageClient::class))
                    ->setFactory([new Reference($options['client']), 'bucket'])
                    ->setArgument(0, $options['bucket'])
                    ->setPublic(false)
            )
            ->setArgument(1, $options['prefix'])
            ->setArgument(2, $visibilityHandlerReference)
            ->setArgument(3, Visibility::PRIVATE)
            ->setArgument(4, null)
            ->setArgument(5, $options['streamReads'])
        ;

        return $adapterId;
    }
}
