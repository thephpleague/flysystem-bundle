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

use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
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
            GoogleCloudStorageAdapter::class => 'league/flysystem-google-cloud-storage',
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
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $bucketDefinition = new Definition();
        $bucketDefinition->setFactory([new Reference($options['client']), 'bucket']);
        $bucketDefinition->setArgument(0, $options['bucket']);

        $definition->setClass(GoogleCloudStorageAdapter::class);
        $definition->setArgument(0, $bucketDefinition);
        $definition->setArgument(1, $options['prefix']);
    }
}
