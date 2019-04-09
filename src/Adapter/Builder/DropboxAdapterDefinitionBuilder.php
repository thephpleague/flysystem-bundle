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

use Spatie\FlysystemDropbox\DropboxAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class DropboxAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'dropbox';
    }

    protected function getRequiredPackages(): array
    {
        return [
            DropboxAdapter::class => 'spatie/flysystem-dropbox',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(DropboxAdapter::class);
        $definition->setArgument(0, new Reference($options['client']));
        $definition->setArgument(1, $options['prefix']);
    }
}
