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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class LocalAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'local';
    }

    protected function getRequiredPackages(): array
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver): void
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

    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        $definition->setClass(LocalFilesystemAdapter::class);
        $definition->setArgument(0, $options['directory']);
        $definition->setArgument(1, $this->createUnixDefinition($options['permissions'], $defaultVisibilityForDirectories ?? Visibility::PRIVATE));
        $definition->setArgument(2, $options['lock']);
        $definition->setArgument(3, $options['skip_links'] ? LocalFilesystemAdapter::SKIP_LINKS : LocalFilesystemAdapter::DISALLOW_LINKS);
        $definition->setArgument(4, null);
        $definition->setArgument(5, $options['lazy_root_creation']);
    }
}
