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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class FtpAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'ftp';
    }

    protected function getRequiredPackages(): array
    {
        return [
            FtpAdapter::class => 'league/flysystem-ftp',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
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
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $options['transferMode'] = $options['transfer_mode'];
        $options['systemType'] = $options['system_type'];
        $options['timestampsOnUnixListingsEnabled'] = $options['timestamps_on_unix_listings_enabled'];
        $options['ignorePassiveAddress'] = $options['ignore_passive_address'];
        $options['recurseManually'] = $options['recurse_manually'];
        unset(
            $options['transfer_mode'],
            $options['system_type'],
            $options['timestamps_on_unix_listings_enabled'],
            $options['ignore_passive_address'],
            $options['recurse_manually']
        );

        $definition->setClass(FtpAdapter::class);
        $definition->setArgument(0,
            (new Definition(FtpConnectionOptions::class))
                ->setFactory([FtpConnectionOptions::class, 'fromArray'])
                ->addArgument($options)
                ->setShared(false)
        );
    }
}
