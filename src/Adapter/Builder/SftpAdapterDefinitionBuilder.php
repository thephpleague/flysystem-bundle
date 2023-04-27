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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class SftpAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'sftp';
    }

    protected function getRequiredPackages(): array
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

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('host');
        $resolver->setAllowedTypes('host', 'string');

        $resolver->setRequired('username');
        $resolver->setAllowedTypes('username', 'string');

        $resolver->setDefault('password', null);
        $resolver->setAllowedTypes('password', ['string', 'null']);

        $resolver->setDefault('port', 22);
        $resolver->setAllowedTypes('port', 'scalar');

        $resolver->setDefault('root', '');
        $resolver->setAllowedTypes('root', 'string');

        $resolver->setDefault('privateKey', null);
        $resolver->setAllowedTypes('privateKey', ['string', 'null']);

        $resolver->setDefault('passphrase', null);
        $resolver->setAllowedTypes('passphrase', ['string', 'null']);

        $resolver->setDefault('hostFingerprint', null);
        $resolver->setAllowedTypes('hostFingerprint', ['string', 'null']);

        $resolver->setDefault('timeout', 90);
        $resolver->setAllowedTypes('timeout', 'scalar');

        $resolver->setDefault('directoryPerm', 0744);
        $resolver->setAllowedTypes('directoryPerm', 'scalar');

        $resolver->setDefault('permPrivate', 0700);
        $resolver->setAllowedTypes('permPrivate', 'scalar');

        $resolver->setDefault('permPublic', 0744);
        $resolver->setAllowedTypes('permPublic', 'scalar');

        $resolver->setDefault('connectivityChecker', null);
        $resolver->setAllowedTypes('connectivityChecker', ['string', 'null']);
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        // Prevent BC
        $adapterFqcn = SftpAdapter::class;
        $connectionFqcn = SftpConnectionProvider::class;
        if (class_exists(SftpAdapterLegacy::class)) {
            $adapterFqcn = SftpAdapterLegacy::class;
            $connectionFqcn = SftpConnectionProviderLegacy::class;
        }

        if (null !== $options['connectivityChecker']) {
            $options['connectivityChecker'] = new Definition($options['connectivityChecker']);
        }

        $definition->setClass($adapterFqcn);
        $definition->setArgument(0,
            (new Definition($connectionFqcn))
                ->setFactory([$connectionFqcn, 'fromArray'])
                ->addArgument($options)
                ->setShared(false)
        );
        $definition->setArgument(1, $options['root']);
    }
}
