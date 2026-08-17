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

use League\Flysystem\PhpseclibV2\SftpAdapter as SftpAdapterLegacy;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider as SftpConnectionProviderLegacy;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\Visibility;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class SftpAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    use UnixPermissionTrait;

    public function getName(): string
    {
        return 'sftp';
    }

    public function getRequiredPackages(): array
    {
        $adapterFqcn = SftpAdapter::class;
        $packageRequire = 'league/flysystem-sftp-v3';

        // Prevent BC
        if (class_exists(SftpAdapterLegacy::class)) {
            trigger_deprecation('league/flysystem-bundle', '2.2', '"league/flysystem-sftp" is deprecated, use "league/flysystem-sftp-v3" instead.');

            $adapterFqcn = SftpAdapterLegacy::class;
            $packageRequire = 'league/flysystem-sftp';
        }

        return [
            $adapterFqcn => $packageRequire,
        ];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('host');
        $resolver->setAllowedTypes('host', 'string');

        $resolver->setRequired('username');
        $resolver->setAllowedTypes('username', 'string');

        $resolver->setDefault('password', null);
        $resolver->setAllowedTypes('password', ['string', 'null']);

        $resolver->setDefault('privateKey', null);
        $resolver->setAllowedTypes('privateKey', ['string', 'null']);

        $resolver->setDefault('passphrase', null);
        $resolver->setAllowedTypes('passphrase', ['string', 'null']);

        $resolver->setDefault('port', 22);
        $resolver->setAllowedTypes('port', 'scalar');

        $resolver->setDefault('useAgent', false);
        $resolver->setAllowedTypes('useAgent', 'bool');

        $resolver->setDefault('timeout', 90);
        $resolver->setAllowedTypes('timeout', 'scalar');

        $resolver->setDefault('maxTries', 4);
        $resolver->setAllowedTypes('maxTries', 'scalar');

        $resolver->setDefault('hostFingerprint', null);
        $resolver->setAllowedTypes('hostFingerprint', ['string', 'array', 'null']);

        $resolver->setDefault('connectivityChecker', null);
        $resolver->setAllowedTypes('connectivityChecker', ['string', 'null']);

        $resolver->setDefault('preferredAlgorithms', []);
        $resolver->setAllowedTypes('preferredAlgorithms', 'array');

        $resolver->setDefault('root', '');
        $resolver->setAllowedTypes('root', 'string');

        $resolver->setDefault('mimeTypeDetector', null);
        $resolver->setAllowedTypes('mimeTypeDetector', ['string', 'null']);

        $resolver->setDefault('detect_mime_type_using_path', false);
        $resolver->setAllowedTypes('detect_mime_type_using_path', 'bool');

        $resolver->setDefault('disconnect_on_destruct', false);
        $resolver->setAllowedTypes('disconnect_on_destruct', 'bool');

        $resolver->setDefault('directoryPerm', 0744);
        $resolver->setAllowedTypes('directoryPerm', 'scalar');
        $resolver->setDeprecated('directoryPerm', 'league/flysystem-bundle', '3.5', 'The "directoryPerm" option is deprecated, use the "permissions" array option instead.');

        $resolver->setDefault('permPrivate', 0700);
        $resolver->setAllowedTypes('permPrivate', 'scalar');
        $resolver->setDeprecated('permPrivate', 'league/flysystem-bundle', '3.5', 'The "permPrivate" option is deprecated, use the "permissions" array option instead.');

        $resolver->setDefault('permPublic', 0744);
        $resolver->setAllowedTypes('permPublic', 'scalar');
        $resolver->setDeprecated('permPublic', 'league/flysystem-bundle', '3.5', 'The "permPublic" option is deprecated, use the "permissions" array option instead.');

        $this->configureUnixOptions($resolver);
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('host')
                    ->isRequired()
                    ->info('SFTP host')
                ->end()
                ->scalarNode('username')
                    ->isRequired()
                    ->info('SFTP username')
                ->end()
                ->scalarNode('password')
                    ->defaultNull()
                    ->info('SFTP password (optional if using private key)')
                ->end()
                ->scalarNode('privateKey')
                    ->defaultNull()
                    ->info('Path to private key file or private key content')
                ->end()
                ->scalarNode('passphrase')
                    ->defaultNull()
                    ->info('Private key passphrase')
                ->end()
                ->integerNode('port')
                    ->defaultValue(22)
                    ->info('SFTP port number')
                ->end()
                ->booleanNode('useAgent')
                    ->defaultFalse()
                    ->info('Use the SSH agent for authentication')
                ->end()
                ->integerNode('timeout')
                    ->defaultValue(90)
                    ->info('Connection timeout in seconds')
                ->end()
                ->integerNode('maxTries')
                    ->defaultValue(4)
                    ->info('Maximum number of connection attempts')
                ->end()
                ->variableNode('hostFingerprint')
                    ->defaultNull()
                    ->info('Host fingerprint for verification (a string, or an array of accepted fingerprints)')
                ->end()
                ->scalarNode('connectivityChecker')
                    ->defaultNull()
                    ->info('Connectivity checker service name')
                ->end()
                ->arrayNode('preferredAlgorithms')
                    ->defaultValue([])
                    ->prototype('variable')
                    ->end()
                    ->info('Preferred algorithms for the SSH connection')
                ->end()
                ->scalarNode('root')
                    ->defaultValue('')
                    ->info('SFTP root directory')
                ->end()
                ->scalarNode('mimeTypeDetector')
                    ->defaultNull()
                    ->info('The mime type detector service name')
                ->end()
                ->booleanNode('detect_mime_type_using_path')
                    ->defaultFalse()
                    ->info('Detect mime type using the file path instead of its content')
                ->end()
                ->booleanNode('disconnect_on_destruct')
                    ->defaultFalse()
                    ->info('Disconnect the SFTP connection when the adapter is destructed')
                ->end()
            ->end()
        ;

        // Add Unix permissions configuration using the trait
        $this->addUnixPermissionsConfiguration($node);
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        // Prevent BC - determine which version to use
        $adapterFqcn = SftpAdapter::class;
        $connectionFqcn = SftpConnectionProvider::class;
        if (class_exists(SftpAdapterLegacy::class)) {
            $adapterFqcn = SftpAdapterLegacy::class;
            $connectionFqcn = SftpConnectionProviderLegacy::class;
        }

        if (!empty($options['connectivityChecker'])) {
            $options['connectivityChecker'] = new Reference($options['connectivityChecker']);
        }

        $root = $options['root'] ?? '';
        unset($options['root']);

        $mimeTypeDetector = null;
        if (null !== ($options['mimeTypeDetector'] ?? null)) {
            $mimeTypeDetector = new Reference($options['mimeTypeDetector']);
        }
        unset($options['mimeTypeDetector']);

        $detectMimeTypeUsingPath = $options['detect_mime_type_using_path'] ?? false;
        unset($options['detect_mime_type_using_path']);

        $disconnectOnDestruct = $options['disconnect_on_destruct'] ?? false;
        unset($options['disconnect_on_destruct']);

        // Create main adapter service
        $container
            ->setDefinition($adapterId, new Definition($adapterFqcn))
            ->setArgument(0,
                (new Definition($adapterFqcn))
                    ->setFactory([$connectionFqcn, 'fromArray'])
                    ->addArgument($options)
                    ->setShared(false)
            )
            ->setArgument(1, $root)
            ->setArgument(2, $this->createUnixDefinition($options['permissions'] ?? [], $defaultVisibilityForDirectories ?? Visibility::PRIVATE))
            ->setArgument(3, $mimeTypeDetector)
            ->setArgument(4, $detectMimeTypeUsingPath)
            ->setArgument(5, $disconnectOnDestruct);

        return $adapterId;
    }
}
