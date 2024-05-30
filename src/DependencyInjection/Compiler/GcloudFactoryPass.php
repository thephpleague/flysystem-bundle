<?php

namespace League\FlysystemBundle\DependencyInjection\Compiler;

use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler;
use League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class GcloudFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!class_exists(GoogleCloudStorageAdapter::class)) {
            return;
        }

        $container->register(PortableVisibilityHandler::class, PortableVisibilityHandler::class);
        $container->setAlias('flysystem.adapter.gcloud.visibility.portable', PortableVisibilityHandler::class);

        $container->register(UniformBucketLevelAccessVisibility::class, UniformBucketLevelAccessVisibility::class);
        $container->setAlias('flysystem.adapter.gcloud.visibility.uniform', UniformBucketLevelAccessVisibility::class);
    }
}
