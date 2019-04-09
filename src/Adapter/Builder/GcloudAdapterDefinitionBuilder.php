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

use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class GcloudAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'gcloud';
    }

    protected function getRequiredPackages(): array
    {
        return [
            GoogleStorageAdapter::class => 'superbalist/flysystem-google-storage',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setRequired('bucket');
        $resolver->setAllowedTypes('bucket', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setDefault('api_url', null);
        $resolver->setAllowedTypes('api_url', ['string', 'null']);
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $bucketDefinition = new Definition();
        $bucketDefinition->setFactory([new Reference($options['client']), 'bucket']);
        $bucketDefinition->setArgument(0, $options['bucket']);

        $definition->setClass(GoogleStorageAdapter::class);
        $definition->setArgument(0, new Reference($options['client']));
        $definition->setArgument(1, $bucketDefinition);
        $definition->setArgument(2, $options['prefix']);
        $definition->setArgument(3, $options['api_url']);
    }
}
