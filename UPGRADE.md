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

# Upgrading from 2.0 to 3.0

flysystem-bundle 3.0 relies on Flysystem 3.0, PHP 8.0+ and Flysystem adapters 3.1+.

The changes in this version focus on dropping support for EOL versions of PHP and Symfony and 
adding support for Azure Blob Storage.  
No new backwards incompatible code changes have been directly introduced by this new bundle version. 
As with any major version upgrade, please be sure to test thoroughly.  

These changes are;  

* Added support for Azure Blob Storage (`league/flysystem-azure-blob-storage ^3.1`)
* Dropped support for PHP 7
* Dropped support of `league/flysystem ^2.0` and older Flysystem adapters `league/flysystem-* ^2.0|3.0.*`
* Dropped support for Symfony 4.2 to 5.3
