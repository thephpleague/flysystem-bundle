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

use League\FlysystemBundle\Adapter\AdapterDefinitionFactory;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
abstract class AbstractAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    protected $definitionFactory;

    public function __construct(AdapterDefinitionFactory $definitionFactory)
    {
        $this->definitionFactory = $definitionFactory;
    }

    final public function createDefinition(array $options): Definition
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $definition = new Definition();
        $definition->setPublic(false);
        $this->configureDefinition($definition, $resolver->resolve($options));

        return $definition;
    }

    abstract protected function configureOptions(OptionsResolver $resolver);

    abstract protected function configureDefinition(Definition $definition, array $options);
}
