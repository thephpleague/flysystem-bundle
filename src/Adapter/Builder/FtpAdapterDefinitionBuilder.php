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

use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
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
final class FtpAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    use UnixPermissionTrait;

    public function getName(): string
    {
        return 'ftp';
    }

    public function getRequiredPackages(): array
    {
        return [
            FtpAdapter::class => 'league/flysystem-ftp',
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

        $resolver->setRequired('password');
        $resolver->setAllowedTypes('password', 'string');

        $resolver->setDefault('port', 21);
        $resolver->setAllowedTypes('port', 'scalar');

        $resolver->setDefault('root', '');
        $resolver->setAllowedTypes('root', 'string');

        $resolver->setDefault('passive', true);
        $resolver->setAllowedTypes('passive', 'scalar');

        $resolver->setDefault('ssl', false);
        $resolver->setAllowedTypes('ssl', 'scalar');

        $resolver->setDefault('timeout', 90);
        $resolver->setAllowedTypes('timeout', 'scalar');

        $resolver->setDefault('ignore_passive_address', null);
        $resolver->setAllowedTypes('ignore_passive_address', ['null', 'bool', 'scalar']);

        $resolver->setDefault('utf8', false);
        $resolver->setAllowedTypes('utf8', 'scalar');

        $resolver->setDefault('transfer_mode', null);
        $resolver->setAllowedTypes('transfer_mode', ['null', 'scalar']);
        $resolver->setAllowedValues('transfer_mode', [null, FTP_ASCII, FTP_BINARY]);

        $resolver->setDefault('system_type', null);
        $resolver->setAllowedTypes('system_type', ['null', 'string']);
        $resolver->setAllowedValues('system_type', [null, 'windows', 'unix']);

        $resolver->setDefault('timestamps_on_unix_listings_enabled', false);
        $resolver->setAllowedTypes('timestamps_on_unix_listings_enabled', 'bool');

        $resolver->setDefault('recurse_manually', true);
        $resolver->setAllowedTypes('recurse_manually', 'bool');

        $resolver->setDefault('use_raw_list_options', null);
        $resolver->setAllowedTypes('use_raw_list_options', ['null', 'bool']);

        $resolver->setDefault('connectivityChecker', null);
        $resolver->setAllowedTypes('connectivityChecker', ['string', 'null']);

        $this->configureUnixOptions($resolver);
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('host')
                    ->isRequired()
                    ->info('FTP host')
                ->end()
                ->scalarNode('username')
                    ->isRequired()
                    ->info('FTP username')
                ->end()
                ->scalarNode('password')
                    ->isRequired()
                    ->info('FTP password')
                ->end()
                ->integerNode('port')
                    ->defaultValue(21)
                    ->info('FTP port number')
                ->end()
                ->scalarNode('root')
                    ->defaultValue('')
                    ->info('FTP root directory')
                ->end()
                ->booleanNode('passive')
                    ->defaultTrue()
                    ->info('Use passive mode')
                ->end()
                ->booleanNode('ssl')
                    ->defaultFalse()
                    ->info('Use SSL/TLS encryption')
                ->end()
                ->integerNode('timeout')
                    ->defaultValue(90)
                    ->info('Connection timeout in seconds')
                ->end()
                ->scalarNode('ignore_passive_address')
                    ->defaultNull()
                    ->info('Ignore passive address')
                ->end()
                ->booleanNode('utf8')
                    ->defaultFalse()
                    ->info('Enable UTF8 mode')
                ->end()
                ->scalarNode('transfer_mode')
                    ->defaultNull()
                    ->info('Transfer mode (FTP_ASCII or FTP_BINARY constante on ftp extension)')
                ->end()
                ->enumNode('system_type')
                    ->values([null, 'windows', 'unix'])
                    ->defaultNull()
                    ->info('FTP system type')
                ->end()
                ->booleanNode('timestamps_on_unix_listings_enabled')
                    ->defaultFalse()
                    ->info('Enable timestamps on Unix listings')
                ->end()
                ->booleanNode('recurse_manually')
                    ->defaultTrue()
                    ->info('Recurse directories manually')
                ->end()
                ->booleanNode('use_raw_list_options')
                    ->defaultNull()
                    ->info('Use raw list options')
                ->end()
                ->scalarNode('connectivityChecker')
                    ->defaultNull()
                    ->info('Connectivity checker service name')
                ->end()
            ->end()
        ;

        // Add Unix permissions configuration using the trait
        $this->addUnixPermissionsConfiguration($node);
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        // Transform options to match FTP adapter expectations
        $options['transferMode'] = $options['transfer_mode'];
        $options['systemType'] = $options['system_type'];
        $options['ignorePassiveAddress'] = $options['ignore_passive_address'];
        $options['timestampsOnUnixListingsEnabled'] = $options['timestamps_on_unix_listings_enabled'];
        $options['recurseManually'] = $options['recurse_manually'];
        $options['useRawListOptions'] = $options['use_raw_list_options'];

        $connectivityChecker = null;
        if (null !== $options['connectivityChecker']) {
            $connectivityChecker = new Reference($options['connectivityChecker']);
        }

        unset(
            $options['transfer_mode'],
            $options['system_type'],
            $options['ignore_passive_address'],
            $options['timestamps_on_unix_listings_enabled'],
            $options['recurse_manually'],
            $options['use_raw_list_options'],
            $options['connectivityChecker']
        );

        $container
            ->setDefinition($adapterId, new Definition(FtpAdapter::class))
            ->setArgument(0,
                (new Definition(FtpConnectionOptions::class))
                    ->setFactory([FtpConnectionOptions::class, 'fromArray'])
                    ->addArgument($options)
                    ->setShared(false)
            )
            ->setArgument(1, null)
            ->setArgument(2, $connectivityChecker)
            ->setArgument(3, $this->createUnixDefinition($options['permissions'] ?? [], $defaultVisibilityForDirectories ?? Visibility::PRIVATE));

        return $adapterId;
    }
}
