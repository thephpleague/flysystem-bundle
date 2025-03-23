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

use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
final class BunnyCDNAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'bunnycdn';
    }

    protected function getRequiredPackages(): array
    {
        return [
            BunnyCDNAdapter::class => 'platformcommunity/flysystem-bunnycdn',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setDefault('pull_zone', '');
        $resolver->setAllowedTypes('pull_zone', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        $definition->setClass(BunnyCDNAdapter::class);
        $definition->setArguments([
            new Reference($options['client']),
            $options['pull_zone'],
        ]);
    }
}
