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
use League\FlysystemBundle\Exception\MissingPackageException;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class AdapterDefinitionFactory
{
    /**
     * @var AdapterDefinitionBuilderInterface[]
     */
    private array $builders;

    /**
     * @param list<AdapterDefinitionBuilderInterface> $builders
     */
    public function __construct(array $builders)
    {
        $this->builders = array_merge([
            new Builder\AsyncAwsAdapterDefinitionBuilder(),
            new Builder\AwsAdapterDefinitionBuilder(),
            new Builder\AzureAdapterDefinitionBuilder(),
            new Builder\FtpAdapterDefinitionBuilder(),
            new Builder\GcloudAdapterDefinitionBuilder(),
            new Builder\GridFSAdapterDefinitionBuilder(),
            new Builder\LocalAdapterDefinitionBuilder(),
            new Builder\MemoryAdapterDefinitionBuilder(),
            new Builder\SftpAdapterDefinitionBuilder(),
            new Builder\WebDAVAdapterDefinitionBuilder(),
            new Builder\BunnyCDNAdapterDefinitionBuilder(),
        ], $builders);
    }

    public function createDefinition(string $name, array $options, ?string $defaultVisibilityForDirectories = null): ?Definition
    {
        foreach ($this->builders as $builder) {
            if ($builder->getName() !== $name) {
                continue;
            }

            $this->ensureRequiredPackagesBuilderAvailable($builder);

            return $builder->createDefinition($options, $defaultVisibilityForDirectories);
        }

        return null;
    }

    private function ensureRequiredPackagesBuilderAvailable(AdapterDefinitionBuilderInterface $builder): void
    {
        $missingPackages = [];
        foreach ($builder->getRequiredPackages() as $requiredClass => $packageName) {
            if (!class_exists($requiredClass)) {
                $missingPackages[] = $packageName;
            }
        }

        if (!$missingPackages) {
            return;
        }

        throw new MissingPackageException(sprintf("Missing package%s, to use the \"%s\" adapter, run:\n\ncomposer require %s", \count($missingPackages) > 1 ? 's' : '', $this->getName(), implode(' ', $missingPackages)));
    }
}
