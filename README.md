# flysystem-bundle

[![Build Status](https://travis-ci.org/thephpleague/flysystem-bundle.svg?branch=master)](https://travis-ci.org/thephpleague/flysystem-bundle)
[![Packagist Version](https://img.shields.io/packagist/v/league/flysystem-bundle.svg?style=flat-square)](https://packagist.org/packages/league/flysystem-bundle)
[![Software license](https://img.shields.io/github/license/thephpleague/flysystem-bundle.svg?style=flat-square)](https://github.com/thephpleague/flysystem-bundle/blob/master/LICENSE)

[![SymfonyInsight](https://insight.symfony.com/projects/525fdfa3-d482-4218-b4b9-3c2efc305fac/big.svg)](https://insight.symfony.com/projects/525fdfa3-d482-4218-b4b9-3c2efc305fac)

flysystem-bundle is a Symfony bundle integrating the [Flysystem](https://flysystem.thephpleague.com)
library into Symfony applications. 

It provides an efficient abstraction for the filesystem in order to change the storage backend depending
on the execution environment (local files in development, cloud storage in production and memory in tests).

## Installation

flysystem-bundle requires PHP 7.1+ and Symfony 4.2+.

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

This means you have two way of using the defined storages:

* either using autowiring, by typehinting against the `FilesystemInterface` and using the
  variable name matching one of your storages:

    ```php
    use League\Flysystem\FilesystemInterface;
    
    class MyService
    {
        private $storage;
        
        // The variable name $defaultStorage matters: it needs to be the camelized version
        // of the name of your storage. 
        public function __construct(FilesystemInterface $defaultStorage)
        {
            $this->storage = $defaultStorage;
        }
        
        // ...
    }
    ```
    
  The same goes for controllers:
    
    ```php
    use League\Flysystem\FilesystemInterface;
    
    class MyController
    {
        // The variable name $defaultStorage matters: it needs to be the camelized version
        // of the name of your storage. 
        public function index(FilesystemInterface $defaultStorage)
        {
            // ...
        }
    }
    ```

* or using manual injection, by injecting the service named `default.storage` inside 
  your services.
  
Once you have a FilesystemInterface, you can call methods from the
[Filesystem API](https://flysystem.thephpleague.com/docs/usage/filesystem-api/)
to interact with your storage.

## Full documentation

1. [Getting started](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/1-getting-started.md)
2. Cloud storage providers:
   [Azure](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md#azure),
   [AWS S3](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md#aws-s3),
   [DigitalOcean Spaces](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md#digitalocean-spaces),
   [Scaleway Object Storage](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md#scaleway-object-storage),
   [Google Cloud Storage](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md#google-cloud-storage),
   [Rackspace](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md#rackspace),
   [WebDAV](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md#webdav)
3. [Interacting with FTP and SFTP servers](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/3-interacting-with-ftp-and-sftp-servers.md)
4. [Caching metadata in Symfony cache](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/4-caching-metadata-in-symfony-cache.md)
5. [Using a lazy adapter to switch storage backend using an environment variable](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/5-using-lazy-adapter-to-switch-at-runtime.md)
6. [Creating a custom adapter](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/6-creating-a-custom-adapter.md)

* [Security issue disclosure procedure](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/A-security-disclosure-procedure.md)
* [Configuration reference](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/B-configuration-reference.md)

## Security Issues

If you discover a security vulnerability within the bundle, please follow
[our disclosure procedure](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/A-security-disclosure-procedure.md).

## Backward Compatibility promise

This library follows the same Backward Compatibility promise as the Symfony framework:
[https://symfony.com/doc/current/contributing/code/bc.html](https://symfony.com/doc/current/contributing/code/bc.html)

> *Note*: many classes in this bundle are either marked `@final` or `@internal`.
> `@internal` classes are excluded from any Backward Compatiblity promise (you should not use them in your code)
> whereas `@final` classes can be used but should not be extended (use composition instead).
