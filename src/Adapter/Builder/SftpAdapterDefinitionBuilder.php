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

use League\Flysystem\Sftp\SftpAdapter;
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
        $definition->setArgument(0, $options);
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
