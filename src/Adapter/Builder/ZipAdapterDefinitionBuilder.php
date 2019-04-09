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

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class ZipAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'zip';
    }

    protected function getRequiredPackages(): array
    {
        return [
            ZipArchiveAdapter::class => 'league/flysystem-ziparchive',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('path');
        $resolver->setAllowedTypes('path', 'string');

        $resolver->setDefault('archive', null);
        $resolver->setAllowedTypes('archive', ['string', 'null']);

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(ZipArchiveAdapter::class);
        $definition->setArgument(0, $options['path']);
        $definition->setArgument(1, $options['archive'] ? new Reference($options['archive']) : null);
        $definition->setArgument(2, $options['prefix']);
    }
}
