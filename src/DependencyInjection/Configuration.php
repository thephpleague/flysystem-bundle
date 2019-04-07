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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('flysystem');
        $rootNode = $this->getRootNode($treeBuilder, 'flysystem');

        $rootNode
            ->children()
                ->scalarNode('default_filesystem')->isRequired()->end()
                ->scalarNode('default_local_directory')->defaultNull()->end()
                ->arrayNode('filesystems')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('adapter')->defaultNull()->end()
                            ->arrayNode('mounts')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->scalarNode('cache')->defaultNull()->end()
                            ->arrayNode('config')
                                ->variablePrototype()
                                ->end()
                            ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function getRootNode(TreeBuilder $treeBuilder, $name)
    {
        // BC layer for symfony/config 4.1 and older
        if (!\method_exists($treeBuilder, 'getRootNode')) {
            return $treeBuilder->root($name);
        }

        return $treeBuilder->getRootNode();
    }
}
