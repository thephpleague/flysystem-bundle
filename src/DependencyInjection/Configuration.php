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

use League\FlysystemBundle\Adapter\Builder\AdapterDefinitionBuilderInterface;
use League\FlysystemBundle\Exception\MissingPackageException;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    /** @var AdapterDefinitionBuilderInterface[] */
    private array $adapterBuilders = [];

    public function __construct(array $adapterBuilders = [])
    {
        $this->adapterBuilders = $adapterBuilders;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('flysystem');
        $rootNode = $treeBuilder->getRootNode();

        $storagesNode = $rootNode
            ->fixXmlConfig('storage')
            ->children()
                ->arrayNode('storages')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->performNoDeepMerging();

        $storageChildren = $storagesNode->children();

        // Legacy format (for backward compatibility)
        $storageChildren
            ->scalarNode('adapter')
                ->info('DEPRECATED: Use the new config format instead (e.g. "local:" instead of "adapter: local")')
            ->end()
            ->arrayNode('options')
                ->info('DEPRECATED: Use the new config format instead')
                ->variablePrototype()->end()
                ->defaultValue([])
            ->end();

        // Add adapter configurations
        foreach ($this->adapterBuilders as $builder) {
            $adapterNode = $storageChildren
                ->arrayNode($builder->getName())
                ->canBeUnset();

            $builder->addConfiguration($adapterNode);
        }

        // Custom adapter service reference
        $storageChildren
            ->scalarNode('service')
                ->info('Reference to a custom adapter service (alternative to registered adapter types)')
            ->end();

        // General storage options
        $storageChildren
            ->scalarNode('visibility')
                ->defaultNull()
                ->info('Default visibility for files')
            ->end()
            ->scalarNode('directory_visibility')
                ->defaultNull()
                ->info('Default visibility for directories')
            ->end()
            ->scalarNode('retain_visibility')
                ->defaultNull()
                ->info('Keeps the original file visibility (public/private) when copying or moving.')
            ->end()
            ->booleanNode('case_sensitive')
                ->defaultTrue()
                ->setDeprecated('league/flysystem-bundle', '3.5', 'The "case_sensitive" option is deprecated and will be removed in 4.0.')
            ->end()
            ->booleanNode('disable_asserts')
                ->defaultFalse()
                ->setDeprecated('league/flysystem-bundle', '3.5', 'The "disable_asserts" option is deprecated and will be removed in 4.0.')
            ->end()
            ->arrayNode('public_url')
                ->beforeNormalization()->castToArray()->end()
                ->defaultValue([])
                ->scalarPrototype()->end()
                ->info('For adapter that do not provide public URLs or override adapter capabilities, a base URL can be configured in the main Filesystem configuration')
            ->end()
            ->scalarNode('path_normalizer')
                ->defaultNull()
                ->info('Path normalizer service name (should implement League\Flysystem\PathNormalizer)')
            ->end()
            ->scalarNode('public_url_generator')
                ->defaultNull()
                ->info('For adapter that do not provide public URLs or override adapter capabilities and public_url option, a public URL generator service name can be configured in the main Filesystem configuration (should implement League\Flysystem\PublicUrlGenerator)')
            ->end()
            ->scalarNode('temporary_url_generator')
                ->defaultNull()
                ->info('For adapter that do not provide public URLs or override adapter capabilities, a temporary URL generator service name can be configured in the main Filesystem configuration (should implement League\Flysystem\TemporaryUrlGenerator)')
            ->end()
            ->booleanNode('read_only')
                ->defaultFalse()
                ->info('Converts a file system to read-only')
            ->end()
        ->end();

        // Validation for exclusive adapter configuration
        $storagesNode
                    ->validate()
                        ->ifTrue(function ($config) {
                            return $this->validateAdapterConfiguration($config);
                        })
                        ->thenInvalid('You must configure exactly one adapter per storage using either the legacy format (adapter + options), the new config format, or a custom service reference')
                    ->end()
                ->end()
                ->defaultValue([])
            ->end()
        ->end();

        return $treeBuilder;
    }

    private function validateAdapterConfiguration(array $config): bool
    {
        $hasLegacyFormat = isset($config['adapter']);
        $hasServiceFormat = isset($config['service']);

        $configuredAdapters = [];
        foreach ($this->adapterBuilders as $builder) {
            if (isset($config[$builder->getName()])) {
                $this->ensureRequiredPackagesAvailable($builder);
                $configuredAdapters[] = $builder->getName();
            }
        }

        $totalConfigurations = ($hasLegacyFormat ? 1 : 0) + ($hasServiceFormat ? 1 : 0) + count($configuredAdapters);

        // Must have exactly one configuration type
        if (1 !== $totalConfigurations) {
            return true;
        }

        return false;
    }

    public static function ensureRequiredPackagesAvailable(AdapterDefinitionBuilderInterface $builder): void
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

        throw new MissingPackageException(sprintf("Missing package%s, to use the \"%s\" adapter, run:\n\ncomposer require %s", \count($missingPackages) > 1 ? 's' : '', $builder->getName(), implode(' ', $missingPackages)));
    }
}
