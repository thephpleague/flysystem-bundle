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

        $resolver->setDefault('password', null);
        $resolver->setAllowedTypes('password', ['string', 'null']);

        $resolver->setDefault('port', 22);
        $resolver->setAllowedTypes('port', 'scalar');

        $resolver->setDefault('root', '');
        $resolver->setAllowedTypes('root', 'string');

        $resolver->setDefault('privateKey', null);
        $resolver->setAllowedTypes('privateKey', ['string', 'null']);

        $resolver->setDefault('timeout', 90);
        $resolver->setAllowedTypes('timeout', 'scalar');

        $resolver->setDefault('directoryPerm', 0744);
        $resolver->setAllowedTypes('directoryPerm', ['scalar', 'string']);

        $resolver->setDefault('permPrivate', 0700);
        $resolver->setAllowedTypes('permPrivate', ['scalar', 'string']);

        $resolver->setDefault('permPublic', 0744);
        $resolver->setAllowedTypes('permPublic', ['scalar', 'string']);
    }

    protected function configureDefinition(Definition $definition, array $options)
    {
        $options['directoryPerm'] = $this->ensureOctalRepresentation($options['directoryPerm']);
        $options['permPrivate'] = $this->ensureOctalRepresentation($options['permPrivate']);
        $options['permPublic'] = $this->ensureOctalRepresentation($options['permPublic']);

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
                ->addArgument([
                    'file' => [
                        'public' => $options['permPublic'],
                        'private' => $options['permPrivate'],
                    ],
                    'dir' => [
                        'public' => 0740,
                        'private' => $options['directoryPerm'],
                    ],
                ])
                ->setShared(false)
        );
    }

    /** @param int|string $value */
    private function ensureOctalRepresentation($value)
    {
        if (is_string($value)) {
            return octdec($value);
        }

        return $value;
    }
}
