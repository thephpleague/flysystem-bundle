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

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Marius Posthumus <mjtheone@gmail.com>
 *
 * @internal
 */
final class ZipArchiveAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'zip';
    }

    protected function getRequiredPackages(): array
    {
        return [
            ZipArchiveAdapter::class => 'league/flysystem-ziparchive',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
    }

    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        $definition->setClass(ZipArchiveAdapter::class);
    }
}
