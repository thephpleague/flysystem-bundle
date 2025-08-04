<?php

use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tests\League\FlysystemBundle\PublicUrlGeneratorMock;
use Tests\League\FlysystemBundle\TemporaryUrlGeneratorMock;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()->public();

    $services->set('custom_adapter', InMemoryFilesystemAdapter::class);

    $services->set('flysystem.test.public_url_generator', PublicUrlGeneratorMock::class);
    $services->set('flysystem.test.temporary_url_generator', TemporaryUrlGeneratorMock::class);

    $services->set('storages_tagged_collection', 'stdClass')
        ->public()
        ->property('locator', tagged_iterator('flysystem.storage', 'storage'));

    // Aliases used to test the services construction
    $services->alias('flysystem.test.fs_asyncaws', 'fs_asyncaws');
    $services->alias('flysystem.test.fs_aws', 'fs_aws');
    $services->alias('flysystem.test.fs_azure', 'fs_azure');
    $services->alias('flysystem.test.fs_custom', 'fs_custom');
    $services->alias('flysystem.test.fs_ftp', 'fs_ftp');
    $services->alias('flysystem.test.fs_gcloud', 'fs_gcloud');
    $services->alias('flysystem.test.fs_lazy', 'fs_lazy');
    $services->alias('flysystem.test.fs_local', 'fs_local');
    $services->alias('flysystem.test.fs_memory', 'fs_memory');
    $services->alias('flysystem.test.fs_sftp', 'fs_sftp');
    $services->alias('flysystem.test.fs_public_url', 'fs_public_url');
    $services->alias('flysystem.test.fs_public_urls', 'fs_public_urls');
    $services->alias('flysystem.test.fs_url_generator', 'fs_url_generator');
    $services->alias('flysystem.test.fs_read_only', 'fs_read_only');
};
