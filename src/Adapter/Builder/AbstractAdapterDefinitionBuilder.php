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

use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
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
    final public function createDefinition(array $options, ?string $defaultVisibilityForDirectories): Definition
    {
        $this->ensureRequiredPackagesAvailable();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $definition = new Definition();
        $definition->setPublic(false);
        $this->configureDefinition($definition, $resolver->resolve($options), $defaultVisibilityForDirectories);

        return $definition;
    }

    abstract protected function getRequiredPackages(): array;

    abstract protected function configureOptions(OptionsResolver $resolver);

    abstract protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories);

    protected function configureUnixOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('permissions', function (OptionsResolver $subResolver) {
            $subResolver->setDefault('file', function (OptionsResolver $permsResolver) {
                $permsResolver->setDefault('public', 0644);
                $permsResolver->setAllowedTypes('public', 'scalar');

                $permsResolver->setDefault('private', 0600);
                $permsResolver->setAllowedTypes('private', 'scalar');
            });

            $subResolver->setDefault('dir', function (OptionsResolver $permsResolver) {
                $permsResolver->setDefault('public', 0755);
                $permsResolver->setAllowedTypes('public', 'scalar');

                $permsResolver->setDefault('private', 0700);
                $permsResolver->setAllowedTypes('private', 'scalar');
            });
        });
    }

    protected function createUnixDefinition(array $permissions, string $defaultVisibilityForDirectories): Definition
    {
        return (new Definition(PortableVisibilityConverter::class))
            ->setFactory([PortableVisibilityConverter::class, 'fromArray'])
            ->addArgument([
                'file' => [
                    'public' => (int) $permissions['file']['public'],
                    'private' => (int) $permissions['file']['private'],
                ],
                'dir' => [
                    'public' => (int) $permissions['dir']['public'],
                    'private' => (int) $permissions['dir']['private'],
                ],
            ])
            ->addArgument($defaultVisibilityForDirectories)
            ->setShared(false)
        ;
    }

    private function ensureRequiredPackagesAvailable(): void
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
