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

use League\Flysystem\Adapter\Ftp;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class FtpAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'ftp';
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('host');
        $resolver->setAllowedTypes('host', 'string');

        $resolver->setRequired('username');
        $resolver->setAllowedTypes('username', 'string');

        $resolver->setRequired('password');
        $resolver->setAllowedTypes('password', 'string');

        $resolver->setDefault('port', 21);
        $resolver->setAllowedTypes('port', 'int');

        $resolver->setDefault('root', '');
        $resolver->setAllowedTypes('root', 'string');

        $resolver->setDefault('passive', true);
        $resolver->setAllowedTypes('passive', 'boolean');

        $resolver->setDefault('ssl', false);
        $resolver->setAllowedTypes('ssl', 'boolean');

        $resolver->setDefault('timeout', 90);
        $resolver->setAllowedTypes('timeout', 'int');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(Ftp::class);
        $definition->setArgument(0, $options);
    }
}
