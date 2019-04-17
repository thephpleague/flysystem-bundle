# flysystem-bundle

[![Build Status](https://travis-ci.org/thephpleague/flysystem-bundle.svg?branch=master)](https://travis-ci.org/thephpleague/flysystem-bundle)
[![Packagist Version](https://img.shields.io/packagist/v/league/flysystem-bundle.svg?style=flat-square)](https://packagist.org/packages/thephpleague/flysystem-bundle)
[![Software license](https://img.shields.io/github/license/thephpleague/flysystem-bundle.svg?style=flat-square)](https://github.com/thephpleague/flysystem-bundle/blob/master/LICENSE)

[![SymfonyInsight](https://insight.symfony.com/projects/525fdfa3-d482-4218-b4b9-3c2efc305fac/big.svg)](https://insight.symfony.com/projects/525fdfa3-d482-4218-b4b9-3c2efc305fac)

This repository is a light Symfony bundle integrating the [Flysystem](https://flysystem.thephpleague.com)
library into Symfony applications. It provides an efficient abstraction for the filesystem,
for instance to use local files in development and a cloud storage in production or to use a memory
filesystem in tests to increase their speed.

This bundle relies on 
[named aliases](https://symfony.com/doc/current/service_container/autowiring.html#dealing-with-multiple-implementations-of-the-same-type) 
(introduced in Symfony 4.2) in order to create and configure multiple filesystems while still 
following the best practices of software architecture (SOLID principles). 

## Installation

flysystem-bundle requires PHP 7.1+ and Symfony 4.2+.

You can install the bundle using Symfony Flex:

```
composer require league/flysystem-bundle
```

## Documentation

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
5. [Creating a custom adapter](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/5-creating-a-custom-adapter.md)

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
