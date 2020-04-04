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

use League\FlysystemBundle\Exception\MissingPackageException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
abstract class AbstractAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    final public function createDefinition(array $options): Definition
    {
        $this->ensureRequiredPackagesAvailable();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $definition = new Definition();
        $definition->setPublic(false);
        $this->configureDefinition($definition, $resolver->resolve($options));

        return $definition;
    }

    abstract protected function getRequiredPackages(): array;

    abstract protected function configureOptions(OptionsResolver $resolver);

    abstract protected function configureDefinition(Definition $definition, array $options);

    private function ensureRequiredPackagesAvailable()
    {
        $missingPackages = [];
        foreach ($this->getRequiredPackages() as $requiredClass => $packageName) {
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
