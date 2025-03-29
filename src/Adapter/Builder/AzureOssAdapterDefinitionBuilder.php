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

use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;
use AzureOss\Storage\Blob\BlobContainerClient;
use AzureOss\Storage\Blob\BlobServiceClient;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
final class AzureOssAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'azureoss';
    }

    protected function getRequiredPackages(): array
    {
        return [
            AzureBlobStorageAdapter::class => 'azure-oss/storage-blob-flysystem',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client');
        $resolver->setAllowedTypes('client', 'string');

        $resolver->setRequired('container');
        $resolver->setAllowedTypes('container', 'string');

        $resolver->setDefault('prefix', '');
        $resolver->setAllowedTypes('prefix', 'string');
    }

    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        $containerClient = new Definition(BlobContainerClient::class);
        $containerClient->setFactory([self::class, 'initializeBlobContainerClient']);
        $containerClient->setArguments([
            new Reference($options['client']),
            $options['container'],
        ]);

        $definition->setClass(AzureBlobStorageAdapter::class);
        $definition->setArgument(0, $containerClient);
        $definition->setArgument(1, $options['prefix']);
    }

    public static function initializeBlobContainerClient(BlobServiceClient $blobServiceClient, string $container): BlobContainerClient
    {
        return $blobServiceClient->getContainerClient($container);
    }
}
