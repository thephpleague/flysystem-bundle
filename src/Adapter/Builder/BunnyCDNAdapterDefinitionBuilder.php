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

use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
final class BunnyCDNAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'bunnycdn';
    }

    public function getRequiredPackages(): array
    {
        return [
            BunnyCDNAdapter::class => 'platformcommunity/flysystem-bunnycdn',
        ];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setDefault('pull_zone', '');
        $resolver->setAllowedTypes('pull_zone', 'string');
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('client')
                    ->isRequired()
                    ->info('The BunnyCDN client service name')
                ->end()
                ->scalarNode('pull_zone')
                    ->defaultValue('')
                    ->info('The BunnyCDN pull zone name')
                ->end()
            ->end()
        ;
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        $container
            ->setDefinition($adapterId, new Definition(BunnyCDNAdapter::class))
            ->setArgument(0, new Reference($options['client']))
            ->setArgument(1, $options['pull_zone']);

        return $adapterId;
    }
}
