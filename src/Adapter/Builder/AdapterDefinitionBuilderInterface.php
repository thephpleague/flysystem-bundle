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

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
interface AdapterDefinitionBuilderInterface
{
    public function getName(): string;

    public function getRequiredPackages(): array;

    /**
     * Add the configuration for this adapter to the configuration tree.
     */
    public function addConfiguration(NodeDefinition $node): void;

    /**
     * Create the adapter service and return its service ID.
     */
    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string;
}
