# flysystem-bundle

[![Packagist Version](https://img.shields.io/packagist/v/league/flysystem-bundle.svg?style=flat-square)](https://packagist.org/packages/league/flysystem-bundle)
[![Software license](https://img.shields.io/github/license/thephpleague/flysystem-bundle.svg?style=flat-square)](LICENSE)

flysystem-bundle is a Symfony bundle integrating the [Flysystem](https://flysystem.thephpleague.com)
library into Symfony applications. 

It provides an efficient abstraction for the filesystem in order to change the storage backend depending
on the execution environment (local files in development, cloud storage in production and memory in tests).

> Note: you are reading the documentation for flysystem-bundle 3.0, which relies on Flysystem 3.  
> If you use Flysystem 1.x, use [flysystem-bundle 1.x](https://github.com/thephpleague/flysystem-bundle/tree/1.x).  
> If you use Flysystem 2.x, use [flysystem-bundle 2.x](https://github.com/thephpleague/flysystem-bundle/tree/2.x).  
> Read the [Upgrade guide](UPGRADE.md) to learn how to upgrade.

## Installation

flysystem-bundle 3.x requires PHP 8.0+ and Symfony 5.4+.

> If you need support for a lower PHP/Symfony version, consider using 
> [flysystem-bundle 2.x](https://github.com/thephpleague/flysystem-bundle/tree/2.x) which support Flysystem 3.x 
> and older PHP/Symfony versions.

You can install the bundle using Symfony Flex:

```
composer require league/flysystem-bundle
```

## Basic usage

The default configuration file created by Symfony Flex provides enough configuration to
use Flysystem in your application as soon as you install the bundle:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        default.storage:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/var/storage/default'
```

This configuration defines a single storage service (`default.storage`) based on the local adapter
and configured to use the `%kernel.project_dir%/var/storage/default` directory.

For each storage defined under `flysystem.storages`, an associated service is created using the
name you provide (in this case, a service `default.storage` will be created). The bundle also
creates a named alias for each of these services.

This means you can inject the storage services in your services and controllers like this:

**1) Using service autowiring:** typehint your service/controller argument with
`FilesystemOperator` and use the `#[Target]` attribute to select the storage by name:

```php
use League\Flysystem\FilesystemOperator;

class MyService
{
    public function __construct(
        #[Target('default.storage')] private FilesystemOperator $storage,
    ) {
    }

    // ...
}
```

Instead of using the `#[Target]` attribute, you can also typehint your service/controller
argument with `FilesystemOperator` and use the camelCase version of your storage
name as the variable name. However, this practice is discouraged and won't work in
future Symfony versions:

```php
use League\Flysystem\FilesystemOperator;

class MyService
{
    private FilesystemOperator $storage;

    // The variable name $defaultStorage matters: it needs to be the
    // camelCase version of the name of your storage (foo.bar.baz -> fooBarBaz)
    public function __construct(FilesystemOperator $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }

    // ...
}
```

**2) Using manual service registration:** in your services, inject the service
that this bundle creates for each of your storages following the pattern
`'flysystem.adapter.'.$storageName`:

```yaml
# config/services.yaml
services:
    # ...

    App\MyService:
        arguments:
            $storage: @flysystem.adapter.default.storage
```
  
Once you have a FilesystemOperator, you can call methods from the
[Filesystem API](https://flysystem.thephpleague.com/v2/docs/usage/filesystem-api/)
to interact with your storage.

If you need to transfer files between the local filesystem and one of your configured storages, the bundle also provides two console commands:

```bash
bin/console flysystem:push <storage> <local-source> [remote-destination]
bin/console flysystem:pull <storage> <remote-source> [local-destination]
```

The `<storage>` argument is the configured Flysystem storage name (for example `default.storage`), not the adapter type. When the destination is omitted, the basename of the source path is used.

## Full documentation

1. [Getting started](docs/1-getting-started.md)
2. Cloud storage providers:
   [AsyncAws S3](docs/2-cloud-storage-providers.md#asyncaws-s3),
   [AWS SDK S3](docs/2-cloud-storage-providers.md#aws-sdk-s3),
   [Azure](docs/2-cloud-storage-providers.md#azure),
   [Google Cloud Storage](docs/2-cloud-storage-providers.md#google-cloud-storage),
   [DigitalOcean Spaces](docs/2-cloud-storage-providers.md#digitalocean-spaces),
   [Scaleway Object Storage](docs/2-cloud-storage-providers.md#scaleway-object-storage)
3. [Interacting with FTP and SFTP servers](docs/3-interacting-with-ftp-and-sftp-servers.md)
4. [Using a lazy adapter to switch storage backend using an environment variable](docs/4-using-lazy-adapter-to-switch-at-runtime.md)
5. [Creating a custom adapter](docs/5-creating-a-custom-adapter.md)
6. [MongoDB GridFS](docs/6-gridfs.md)
7. [WebDAV](docs/7-webdav.md)
8. [BunnyCDN](docs/8-bunnycdn.md)

* [Security issue disclosure procedure](docs/A-security-disclosure-procedure.md)

## Security Issues

If you discover a security vulnerability within the bundle, please follow
[our disclosure procedure](docs/A-security-disclosure-procedure.md).

## Backward Compatibility promise

This library follows the same Backward Compatibility promise as the Symfony framework:
[https://symfony.com/doc/current/contributing/code/bc.html](https://symfony.com/doc/current/contributing/code/bc.html)

> *Note*: many classes in this bundle are either marked `@final` or `@internal`.
> `@internal` classes are excluded from any Backward Compatibility promise (you should not use them in your code)
> whereas `@final` classes can be used but should not be extended (use composition instead).
