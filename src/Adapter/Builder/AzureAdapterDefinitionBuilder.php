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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class AzureAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'azure';
    }

    protected function getRequiredPackages(): array
    {
        return [
            AzureBlobStorageAdapter::class => 'league/flysystem-azure-blob-storage',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setRequired('container');
        $resolver->setAllowedTypes('container', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(AzureBlobStorageAdapter::class);
        $definition->setArgument(0, new Reference($options['client']));
        $definition->setArgument(1, $options['container']);
        $definition->setArgument(2, $options['prefix']);
    }
}
