<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('flysystem');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('storage')
            ->children()
                ->arrayNode('storages')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->performNoDeepMerging()
                        ->children()
                            ->scalarNode('adapter')->isRequired()->end()
                            ->arrayNode('options')
                                ->variablePrototype()
                                ->end()
                            ->defaultValue([])
                            ->end()
                            ->scalarNode('visibility')->defaultNull()->end()
                            ->scalarNode('directory_visibility')->defaultNull()->end()
                            ->booleanNode('case_sensitive')->defaultTrue()->end()
                            ->booleanNode('disable_asserts')->defaultFalse()->end()
                            ->arrayNode('public_url')
                                ->beforeNormalization()->castToArray()->end()
                                ->defaultValue([])
                                ->scalarPrototype()->end()
                            ->end()
                            ->scalarNode('public_url_generator')->defaultNull()->end()
                            ->scalarNode('temporary_url_generator')->defaultNull()->end()
                        ->end()
                    ->end()
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
