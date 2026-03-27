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

use League\Flysystem\WebDAV\WebDAVAdapter;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kévin Dunglas <kevin@dunglas.dev>
 *
 * @internal
 */
final class WebDAVAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'webdav';
    }

    public function getRequiredPackages(): array
    {
        return [
            WebDAVAdapter::class => 'league/flysystem-webdav',
        ];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setDefault('visibility_handling', 'throw');
        $resolver->setAllowedValues('visibility_handling', ['throw', 'ignore']);

        $resolver->setDefault('manual_copy', false);
        $resolver->setAllowedTypes('manual_copy', 'bool');

        $resolver->setDefault('manual_move', false);
        $resolver->setAllowedTypes('manual_move', 'bool');
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('client')
                    ->isRequired()
                    ->info('The WebDAV client service name')
                ->end()
                ->scalarNode('prefix')
                    ->defaultValue('')
                    ->info('Optional path prefix to prepend to all paths')
                ->end()
                ->enumNode('visibility_handling')
                    ->values(['throw', 'ignore'])
                    ->defaultValue('throw')
                    ->info('How to handle visibility operations')
                ->end()
                ->booleanNode('manual_copy')
                    ->defaultFalse()
                    ->info('Whether to handle copy operations manually')
                ->end()
                ->booleanNode('manual_move')
                    ->defaultFalse()
                    ->info('Whether to handle move operations manually')
                ->end()
            ->end()
        ;
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.webdav.'.$storageName;

        $container
            ->setDefinition($adapterId, new Definition(WebDAVAdapter::class))
            ->setArgument(0, new Reference($options['client']))
            ->setArgument(1, $options['prefix'])
            ->setArgument(2, $options['visibility_handling'])
            ->setArgument(3, $options['manual_copy'])
            ->setArgument(4, $options['manual_move']);

        return $adapterId;
    }
}
