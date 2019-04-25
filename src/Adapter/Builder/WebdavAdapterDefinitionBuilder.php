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

use League\Flysystem\WebDAV\WebDAVAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class WebdavAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'webdav';
    }

    protected function getRequiredPackages(): array
    {
        return [
            WebDAVAdapter::class => 'league/flysystem-webdav',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setDefault('use_stream_copy', true);
        $resolver->setAllowedTypes('use_stream_copy', 'scalar');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(WebDAVAdapter::class);
        $definition->setArgument(0, new Reference($options['client']));
        $definition->setArgument(1, $options['prefix']);
        $definition->setArgument(2, $options['use_stream_copy']);
    }
}
