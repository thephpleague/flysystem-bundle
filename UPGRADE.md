# Upgrading from 2.0 to 3.0

flysystem-bundle 3.0 dropped support for End-Of-Life versions of PHP, Symfony and Flysystem.

No backward incompatible code change have been directly introduced by this bundle version.
You should read the [Flysystem 3.x changelog](https://github.com/thephpleague/flysystem/blob/3.x/CHANGELOG.md)
for any indirect change that may affect you.

The list of changes is the following:

* Dropped support for PHP 7.x ;
* Dropped support for Symfony 4.2 to 5.3 ;
* Dropped support of Flysystem 2.x ;
* Added support for Azure Blob Storage (`league/flysystem-azure-blob-storage ^3.1`)

# Upgrading from 1.0 to 2.0

flysystem-bundle 2.0 introduces backward incompatible changes, meaning you will need to update
the code of your projects to upgrade.

flysystem-bundle 2.0 relies on Flysystem 2.0, which introduced most of the backward incompatible
changes. You should read
[Flysystem 2.0 upgrade guide](https://flysystem.thephpleague.com/v2/docs/advanced/upgrade-to-2.0.0/).

In addition to the library updates, the bundle also changed a bit:

* Add official support for PHP 8.x ;
* Migration to AsyncAWS 1.0 ;
* Drop support for PHP 7.1 ;
* Drop support for Azure, Dropbox, Rackspace and WebDAV adapters (following the main library) ;
* Drop support for null, cache, zip and replicate adapters (following the main library) ;
* Drop support for plugins ;
