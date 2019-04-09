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

use League\Flysystem\Adapter\NullAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class NullAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'null';
    }

    protected function getRequiredPackages(): array
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(NullAdapter::class);
    }
}
