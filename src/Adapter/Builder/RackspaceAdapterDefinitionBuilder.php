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

use League\Flysystem\Rackspace\RackspaceAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class RackspaceAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'rackspace';
    }

    protected function getRequiredPackages(): array
    {
        return [
            RackspaceAdapter::class => 'league/flysystem-rackspace',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('container');
        $resolver->setAllowedTypes('container', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(RackspaceAdapter::class);
        $definition->setArgument(0, new Reference($options['container']));
        $definition->setArgument(1, $options['prefix']);
    }
}
