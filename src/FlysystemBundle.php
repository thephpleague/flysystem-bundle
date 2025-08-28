<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle;

use League\FlysystemBundle\Adapter\Builder;
use League\FlysystemBundle\DependencyInjection\Compiler\LazyFactoryPass;
use League\FlysystemBundle\DependencyInjection\FlysystemExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class FlysystemBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        /** @var FlysystemExtension $extension */
        $extension = $container->getExtension('flysystem');
        $extension->addAdapterDefinitionBuilder(new Builder\AsyncAwsAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\AwsAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\AzureAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\FtpAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\GcloudAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\GridFSAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\LazyAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\LocalAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\MemoryAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\SftpAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\WebDAVAdapterDefinitionBuilder());
        $extension->addAdapterDefinitionBuilder(new Builder\BunnyCDNAdapterDefinitionBuilder());

        $container->addCompilerPass(new LazyFactoryPass());
    }
}
