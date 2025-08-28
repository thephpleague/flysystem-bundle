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

use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Visibility;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class LocalAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    use UnixPermissionTrait;

    public function getName(): string
    {
        return 'local';
    }

    public function getRequiredPackages(): array
    {
        return [];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('directory');
        $resolver->setAllowedTypes('directory', 'string');

        $this->configureUnixOptions($resolver);

        $resolver->setDefault('lock', 0);
        $resolver->setAllowedTypes('lock', 'scalar');

        $resolver->setDefault('skip_links', false);
        $resolver->setAllowedTypes('skip_links', 'scalar');

        $resolver->setDefault('lazy_root_creation', false);
        $resolver->setAllowedTypes('lazy_root_creation', 'scalar');
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('directory')
                    ->isRequired()
                    ->info('Directory path for local storage')
                ->end()

                ->integerNode('lock')
                    ->defaultValue(0)
                    ->info('Lock flags for file operations')
                ->end()

                ->booleanNode('skip_links')
                    ->defaultFalse()
                    ->info('Whether to skip symbolic links')
                ->end()

                ->booleanNode('lazy_root_creation')
                    ->defaultFalse()
                    ->info('Whether to create the root directory lazily')
                ->end()
            ->end()
        ;

        // Add Unix permissions configuration using the trait
        $this->addUnixPermissionsConfiguration($node);
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        $container
            ->setDefinition($adapterId, new Definition(LocalFilesystemAdapter::class))
            ->setArgument(0, $options['directory'])
            ->setArgument(1, $this->createUnixDefinition($options['permissions'] ?? [], $defaultVisibilityForDirectories ?? Visibility::PRIVATE))
            ->setArgument(2, $options['lock'] ?? 0)
            ->setArgument(3, ($options['skip_links'] ?? false) ? LocalFilesystemAdapter::SKIP_LINKS : LocalFilesystemAdapter::DISALLOW_LINKS)
            ->setArgument(4, null)
            ->setArgument(5, $options['lazy_root_creation'] ?? false);

        return $adapterId;
    }
}
