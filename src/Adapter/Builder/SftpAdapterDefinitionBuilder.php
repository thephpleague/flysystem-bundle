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

use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
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
        return [
            SftpAdapter::class => 'league/flysystem-sftp',
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

        $resolver->setDefault('port', 22);
        $resolver->setAllowedTypes('port', 'scalar');

        $resolver->setDefault('root', '');
        $resolver->setAllowedTypes('root', 'string');

        $resolver->setDefault('privateKey', null);
        $resolver->setAllowedTypes('privateKey', ['string', 'null']);

        $resolver->setDefault('timeout', 90);
        $resolver->setAllowedTypes('timeout', 'scalar');

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

        $resolver->setDefault('directoryPerm', 0744);
        $resolver->setDeprecated('directoryPerm', 'league/flysystem-bundle', '2.0.1', 'The option "directoryPerm" is deprecated, use "permissions.dir.public" and "permissions.dir.private" instead.');
        $resolver->setAllowedTypes('directoryPerm', 'scalar');

        $resolver->setDefault('permPrivate', 0700);
        $resolver->setDeprecated('permPrivate', 'league/flysystem-bundle', '2.0.1', 'The option "permPrivate" is deprecated, use "permissions.file.private" instead.');
        $resolver->setAllowedTypes('permPrivate', 'scalar');

        $resolver->setDefault('permPublic', 0744);
        $resolver->setDeprecated('permPublic', 'league/flysystem-bundle', '2.0.1', 'The option "permPublic" is deprecated, use "permissions.file.public" instead.');
        $resolver->setAllowedTypes('permPublic', 'scalar');
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $definition->setClass(SftpAdapter::class);
        $definition->setArgument(0,
            (new Definition(SftpConnectionProvider::class))
                ->setFactory([SftpConnectionProvider::class, 'fromArray'])
                ->addArgument($options)
                ->setShared(false)
        );
        $definition->setArgument(1, $options['root']);
        $definition->setArgument(2,
            (new Definition(PortableVisibilityConverter::class))
                ->setFactory([PortableVisibilityConverter::class, 'fromArray'])
                ->addArgument($options['permissions'])
                ->setShared(false)
        );
    }
}
