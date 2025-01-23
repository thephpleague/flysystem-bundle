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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kévin Dunglas <kevin@dunglas.dev>
 *
 * @internal
 */
final class WebDAVAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'webdav';
    }

    protected function getRequiredPackages(): array
    {
        return [
            WebDAVAdapter::class => 'league/flysystem-webdav',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setDefault('visibility_handling', WebDAVAdapter::ON_VISIBILITY_THROW_ERROR);
        $resolver->setAllowedTypes('visibility_handling', ['string']);

        $resolver->setDefault('manual_copy', false);
        $resolver->setAllowedTypes('manual_copy', 'bool');

        $resolver->setDefault('manual_move', false);
        $resolver->setAllowedTypes('manual_move', 'bool');
    }

    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        $definition->setClass(WebDAVAdapter::class);
        $definition->setArguments([
            new Reference($options['client']),
            $options['prefix'],
            $options['visibility_handling'],
            $options['manual_copy'],
            $options['manual_move'],
        ]);
    }
}
