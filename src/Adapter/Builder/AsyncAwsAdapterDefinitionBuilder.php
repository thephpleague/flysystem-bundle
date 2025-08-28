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

use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\AsyncAwsS3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal
 */
final class AsyncAwsAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'asyncaws';
    }

    public function getRequiredPackages(): array
    {
        return [
            AsyncAwsS3Adapter::class => 'league/flysystem-async-aws-s3',
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
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('client')
                    ->isRequired()
                    ->info('The AsyncAws S3 client service name')
                ->end()
                ->scalarNode('bucket')
                    ->isRequired()
                    ->info('The name of the AWS S3 bucket')
                ->end()
                ->scalarNode('prefix')
                    ->defaultValue('')
                    ->info('Optional path prefix to prepend to all object keys')
                ->end()
            ->end()
        ;
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        $container
            ->setDefinition($adapterId, new Definition(AsyncAwsS3Adapter::class))
            ->setArgument(0, new Reference($options['client']))
            ->setArgument(1, $options['bucket'])
            ->setArgument(2, $options['prefix'])
            ->setArgument(3,
                (new Definition(PortableVisibilityConverter::class))
                    ->setArgument(0, $defaultVisibilityForDirectories ?? Visibility::PUBLIC)
                    ->setShared(false)
            );

        return $adapterId;
    }
}
