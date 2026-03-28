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

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\FilesystemWriter;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use League\FlysystemBundle\Adapter\Builder\AdapterDefinitionBuilderInterface;
use League\FlysystemBundle\Command\PullCommand;
use League\FlysystemBundle\Command\PushCommand;
use League\FlysystemBundle\Exception\MissingPackageException;
use League\FlysystemBundle\Lazy\LazyFactory;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class FlysystemExtension extends Extension implements PrependExtensionInterface
{
    /** @var list<AdapterDefinitionBuilderInterface> */
    private array $adapterDefinitionBuilders = [];

    /** @var array<string, AdapterDefinitionBuilderInterface>|null */
    private ?array $adapterDefinitionBuildersCache = null;

    public function prepend(ContainerBuilder $container): void
    {
        foreach ($this->getAdapterDefinitionBuilders() as $builder) {
            if ($builder instanceof PrependExtensionInterface) {
                $builder->prepend($container);
            }
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return new Configuration($this->getAdapterDefinitionBuilders());
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $container
            ->setDefinition('flysystem.adapter.lazy.factory', new Definition(LazyFactory::class))
            ->setPublic(false)
        ;

        if (ContainerBuilder::willBeAvailable('symfony/console', Command::class, ['symfony/framework-bundle'])) {
            $this->registerPushCommand($container);
            $this->registerPullCommand($container);
        }

        $this->createStoragesDefinitions($config, $container);
    }

    public function addAdapterDefinitionBuilder(AdapterDefinitionBuilderInterface $builder): void
    {
        $this->adapterDefinitionBuilders[] = $builder;
        // Invalidate cache when adding new builder
        $this->adapterDefinitionBuildersCache = null;
    }

    private function registerPushCommand(ContainerBuilder $container): void
    {
        $container
            ->register(PushCommand::class, PushCommand::class)
            ->setPublic(false)
            ->setArgument('$storages', tagged_locator('flysystem.storage', 'storage'))
            ->addTag('console.command')
        ;
    }

    private function registerPullCommand(ContainerBuilder $container): void
    {
        $container
            ->register(PullCommand::class, PullCommand::class)
            ->setPublic(false)
            ->setArgument('$storages', tagged_locator('flysystem.storage', 'storage'))
            ->addTag('console.command')
        ;
    }

    private function createStoragesDefinitions(array $config, ContainerBuilder $container): void
    {
        foreach ($config['storages'] as $storageName => $storageConfig) {
            // Resolve adapter type and options from either legacy or new format
            $adapterType = $this->resolveAdapterType($storageConfig);
            $adapterOptions = $this->resolveAdapterOptions($storageConfig, $adapterType);

            // Create adapter definition
            $adapterId = $this->createAdapterDefinition($container, $adapterType, $storageName, $adapterOptions, $storageConfig['directory_visibility'] ?? null, isset($storageConfig['adapter']));

            // Special case for lazy adapter: it handles storage creation internally
            if (null === $adapterId && 'lazy' === $adapterType) {
                // The LazyAdapterDefinitionBuilder has already created the storage definition
                // We register all autowiring aliases here for consistency
                $container->registerAliasForArgument($storageName, FilesystemOperator::class, $storageName)->setPublic(false);
                $container->registerAliasForArgument($storageName, FilesystemReader::class, $storageName)->setPublic(false);
                $container->registerAliasForArgument($storageName, FilesystemWriter::class, $storageName)->setPublic(false);

                continue;
            }

            if (null === $adapterId) {
                // Custom adapter
                $container->setAlias($adapterId = 'flysystem.adapter.'.$storageName, $adapterType)->setPublic(false);
            }

            // Create ReadOnly adapter
            if ($storageConfig['read_only']) {
                if (!class_exists(ReadOnlyFilesystemAdapter::class)) {
                    throw new MissingPackageException("Missing package, to use the readonly option, run:\n\ncomposer require league/flysystem-read-only");
                }

                $originalAdapterId = $adapterId;
                $container->setDefinition(
                    $adapterId = $adapterId.'.read_only',
                    $this->createReadOnlyAdapterDefinition(new Reference($originalAdapterId))
                );
            }

            // Create storage definition
            $container->setDefinition(
                $storageName,
                $this->createStorageDefinition($storageName, new Reference($adapterId), $storageConfig)
            );

            // Register named autowiring alias
            $container->registerAliasForArgument($storageName, FilesystemOperator::class, $storageName)->setPublic(false);
            $container->registerAliasForArgument($storageName, FilesystemReader::class, $storageName)->setPublic(false);
            $container->registerAliasForArgument($storageName, FilesystemWriter::class, $storageName)->setPublic(false);
        }
    }

    private function createStorageDefinition(string $storageName, Reference $adapter, array $config): Definition
    {
        $publicUrl = null;
        if ($config['public_url']) {
            $publicUrl = 1 === count($config['public_url']) ? $config['public_url'][0] : $config['public_url'];
        }

        $definition = new Definition(Filesystem::class);
        $definition->setPublic(false);
        $definition->setArgument(0, $adapter);
        $definition->setArgument(1, [
            'visibility' => $config['visibility'],
            'directory_visibility' => $config['directory_visibility'],
            'retain_visibility' => $config['retain_visibility'],
            'case_sensitive' => $config['case_sensitive'],
            'disable_asserts' => $config['disable_asserts'],
            'public_url' => $publicUrl,
        ]);
        $definition->setArgument(2, $config['path_normalizer'] ? new Reference($config['path_normalizer']) : null);
        $definition->setArgument(3, $config['public_url_generator'] ? new Reference($config['public_url_generator']) : null);
        $definition->setArgument(4, $config['temporary_url_generator'] ? new Reference($config['temporary_url_generator']) : null);
        $definition->addTag('flysystem.storage', ['storage' => $storageName]);

        return $definition;
    }

    private function createReadOnlyAdapterDefinition(Reference $adapter): Definition
    {
        $definition = new Definition(ReadOnlyFilesystemAdapter::class);
        $definition->setPublic(false);
        $definition->setArgument(0, $adapter);

        return $definition;
    }

    /**
     * Get adapter builders indexed by name with caching.
     *
     * @return array<string, AdapterDefinitionBuilderInterface>
     */
    private function getAdapterDefinitionBuilders(): array
    {
        if (null === $this->adapterDefinitionBuildersCache) {
            $this->adapterDefinitionBuildersCache = [];
            foreach ($this->adapterDefinitionBuilders as $builder) {
                $this->adapterDefinitionBuildersCache[$builder->getName()] = $builder;
            }
        }

        return $this->adapterDefinitionBuildersCache;
    }

    private function createAdapterDefinition(ContainerBuilder $container, string $type, string $storageName, array $options, ?string $defaultVisibilityForDirectories = null, bool $isLegacyFormat = false): ?string
    {
        $builders = $this->getAdapterDefinitionBuilders();

        $builder = $builders[$type] ?? null;
        if (null === $builder) {
            return null;
        }

        // For legacy format
        if ($isLegacyFormat && method_exists($builder, 'configureOptions')) {
            Configuration::ensureRequiredPackagesAvailable($builder);

            $resolver = new OptionsResolver();
            $builder->configureOptions($resolver);
            $options = $resolver->resolve($options);
        }

        return $builder->createAdapter($container, $storageName, $options, $defaultVisibilityForDirectories);
    }

    private function resolveAdapterType(array $config): string
    {
        // Legacy format
        if (isset($config['adapter'])) {
            trigger_deprecation(
                'league/flysystem-bundle',
                '3.5',
                'Using the legacy format with "adapter" and "options" keys is deprecated. Use the new discoverable format instead. See the migration guide for details.'
            );

            return $config['adapter'];
        }

        // New discoverable format - check for registered builders first
        $builders = $this->getAdapterDefinitionBuilders();
        foreach ($builders as $name => $builder) {
            if (isset($config[$name])) {
                return $name;
            }
        }

        // If no registered builder found, check for 'service' key (custom adapter)
        if (isset($config['service'])) {
            return $config['service'];
        }

        throw new \InvalidArgumentException('No adapter configured. Use either a registered adapter type or specify a "service" key for custom adapters.');
    }

    private function resolveAdapterOptions(array $config, string $adapterType): array
    {
        // Legacy format
        if (!empty($config['options'])) {
            return $config['options'];
        }

        // New discoverable format
        return $config[$adapterType] ?? [];
    }
}
