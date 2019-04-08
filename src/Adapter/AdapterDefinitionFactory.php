<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\Adapter;

use League\FlysystemBundle\Adapter\Builder\AdapterDefinitionBuilderInterface;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class AdapterDefinitionFactory
{
    /**
     * @var AdapterDefinitionBuilderInterface[]
     */
    private $builders;

    public function __construct()
    {
        $this->builders = [
            new Builder\AwsAdapterDefinitionBuilder($this),
            new Builder\AzureAdapterDefinitionBuilder($this),
            new Builder\DropboxAdapterDefinitionBuilder($this),
            new Builder\GcloudAdapterDefinitionBuilder($this),
            new Builder\LocalAdapterDefinitionBuilder($this),
            new Builder\PhpcrAdapterDefinitionBuilder($this),
            new Builder\RackspaceAdapterDefinitionBuilder($this),
            new Builder\WebdavAdapterDefinitionBuilder($this),
            new Builder\ZipAdapterDefinitionBuilder($this),
        ];
    }

    public function createDefinition(string $name, array $options): ?Definition
    {
        foreach ($this->builders as $builder) {
            if ($builder->getName() === $name) {
                return $builder->createDefinition($options);
            }
        }

        return null;
    }
}
