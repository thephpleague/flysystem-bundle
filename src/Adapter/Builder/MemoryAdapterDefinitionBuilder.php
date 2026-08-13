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

use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
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
final class MemoryAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'memory';
    }

    public function getRequiredPackages(): array
    {
        return [
            InMemoryFilesystemAdapter::class => 'league/flysystem-memory',
        ];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('mimeTypeDetector', null);
        $resolver->setAllowedTypes('mimeTypeDetector', ['string', 'null']);
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('mimeTypeDetector')
                    ->defaultNull()
                    ->info('The mime type detector service name')
                ->end()
            ->end()
            ->info('In-memory adapter for testing')
        ;
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        $mimeTypeDetector = null;
        if (null !== ($options['mimeTypeDetector'] ?? null)) {
            $mimeTypeDetector = new Reference($options['mimeTypeDetector']);
        }

        $container
            ->setDefinition($adapterId, new Definition(InMemoryFilesystemAdapter::class))
            ->setArgument(0, $defaultVisibilityForDirectories ?? Visibility::PUBLIC)
            ->setArgument(1, $mimeTypeDetector);

        return $adapterId;
    }
}
