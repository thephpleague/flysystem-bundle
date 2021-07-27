# Upgrading from 1.0 to 2.0

flysystem-bundle 2.0 introduces backward incompatible changes, meaning you will need to update
the code of your projects to upgrade.

flysystem-bundle 2.0 relies on Flysystem 2.0, which introduced most of the backward incompatible
changes. You should read
[Flysystem 2.0 upgrade guide](https://flysystem.thephpleague.com/v2/docs/advanced/upgrade-to-2.0.0/).

In addition to the library updates, the bundle also changed a bit:

* Add official support for PHP 8 ;
* Migration to AsyncAWS 1.0 ;
* Drop support for PHP 7.1 ;
* Drop support for Azure, Dropbox, Rackspace and WebDAV adapters (following the main library) ;
* Drop support for null, cache, zip and replicate adapters (following the main library) ;
* Drop support for plugins ;
