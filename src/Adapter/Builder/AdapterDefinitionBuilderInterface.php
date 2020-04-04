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

use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
interface AdapterDefinitionBuilderInterface
{
    public function getName(): string;

    /**
     * Create the definition for this builder's adapter given an array of options.
     */
    public function createDefinition(array $options): Definition;
}
