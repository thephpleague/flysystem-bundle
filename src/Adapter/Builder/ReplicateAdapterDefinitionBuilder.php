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

use League\Flysystem\Replicate\ReplicateAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class ReplicateAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'replicate';
    }

    protected function getRequiredPackages(): array
    {
        return [
            ReplicateAdapter::class => 'league/flysystem-replicate-adapter',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('source');
        $resolver->setAllowedTypes('source', 'string');

        $resolver->setRequired('replica');
        $resolver->setAllowedTypes('replica', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(ReplicateAdapter::class);
        $definition->setArgument(0, new Reference('flysystem.adapter.'.$options['source']));
        $definition->setArgument(1, new Reference('flysystem.adapter.'.$options['replica']));
    }
}
