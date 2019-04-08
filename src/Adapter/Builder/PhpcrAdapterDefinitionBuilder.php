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

use League\Flysystem\Phpcr\PhpcrAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class PhpcrAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'phpcr';
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('session');
        $resolver->setAllowedTypes('session', 'string');

        $resolver->setRequired('root');
        $resolver->setAllowedTypes('root', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(PhpcrAdapter::class);
        $definition->setArgument(0, new Reference($options['session']));
        $definition->setArgument(1, $options['root']);
    }
}
