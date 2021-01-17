# Upgrading from 1.0 to 2.0

flysystem-bundle 2.0 introduces Backward Compatibility breaks, meaning you will need to update
the code of your projects to upgrade.

flysystem-bundle 2.0 relies on Flysystem 2.0, which introduced most of the backward incompatible
changes. You should read
[Flysystem 2.0 upgrade guide](https://flysystem.thephpleague.com/v2/docs/advanced/upgrade-to-2.0.0/).

In addition to the library updates, the bundle also changed a bit:

* Following the drop of support for several Cloud providers in Flysystem itself, the bundle no longer
  natively supports Azure, Dropbox, Rackspace and WebDAV. If the need arise for community adapters to be
  ported to Flysystem 2 and flysystem-bundle 2, we could add them again.
* Following their removal from the main library, null, cache, zip and replicate adapters were removed 
  from the bundle as well.
* Following the drop of support for plugins in the main library, the bundle does not support them anymore.
