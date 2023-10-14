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

use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\AsyncAwsS3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal
 */
class AsyncAwsAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'asyncaws';
    }

    protected function getRequiredPackages(): array
    {
        return [
            AsyncAwsS3Adapter::class => 'league/flysystem-async-aws-s3',
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
    }

    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        $definition->setClass(AsyncAwsS3Adapter::class);
        $definition->setArgument(0, new Reference($options['client']));
        $definition->setArgument(1, $options['bucket']);
        $definition->setArgument(2, $options['prefix']);
        $definition->setArgument(3,
            (new Definition(PortableVisibilityConverter::class))
                ->setArgument(0, $defaultVisibilityForDirectories ?? Visibility::PUBLIC)
                ->setShared(false)
        );
    }
}
