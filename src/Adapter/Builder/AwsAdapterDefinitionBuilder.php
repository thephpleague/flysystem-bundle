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

use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class AwsAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'aws';
    }

    protected function getRequiredPackages(): array
    {
        return [
            AwsS3V3Adapter::class => 'league/flysystem-aws-s3-v3',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setRequired('bucket');
        $resolver->setAllowedTypes('bucket', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setDefault('options', []);
        $resolver->setAllowedTypes('options', 'array');

        $resolver->setDefault('streamReads', true);
        $resolver->setAllowedTypes('streamReads', 'bool');
    }

    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        $definition->setClass(AwsS3V3Adapter::class);
        $definition->setArgument(0, new Reference($options['client']));
        $definition->setArgument(1, $options['bucket']);
        $definition->setArgument(2, $options['prefix']);
        $definition->setArgument(3,
            (new Definition(PortableVisibilityConverter::class))
                ->setArgument(0, $defaultVisibilityForDirectories ?? Visibility::PUBLIC)
                ->setShared(false)
        );
        $definition->setArgument(4, null);
        $definition->setArgument(5, $options['options']);
        $definition->setArgument(6, $options['streamReads']);
    }
}
