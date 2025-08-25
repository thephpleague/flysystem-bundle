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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 *
 * @author Maxime Hélias <maximehelias16@gmail.com>
 */
trait UnixPermissionTrait
{
    protected function configureUnixOptions(OptionsResolver $resolver): void
    {
        $method = method_exists($resolver, 'setOptions') ? 'setOptions' : 'setDefault';

        $resolver->$method('permissions', function (OptionsResolver $subResolver) use ($method) {
            $subResolver->$method('file', function (OptionsResolver $permsResolver) {
                $permsResolver->setDefault('public', 0644);
                $permsResolver->setAllowedTypes('public', 'scalar');

                $permsResolver->setDefault('private', 0600);
                $permsResolver->setAllowedTypes('private', 'scalar');
            });

            $subResolver->$method('dir', function (OptionsResolver $permsResolver) {
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
}
