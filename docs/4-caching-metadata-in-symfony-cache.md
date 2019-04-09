# Caching metadata in Symfony cache

Networking and I/O are often the source of performance problems in applications.
On top of this, Flysystem generally aims to be as reliable as possible, which 
means it will often check and validate operations before they are done. This
involves an additional overhead on top of the classical slowness of I/O.

When your application needs to scale, you may need to cache metadata in order to
improve performances on this level. To do this, you can use the `cache` adapter
in this bundle.

### Installation

```
composer require league/flysystem-cached-adapter
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage.source:
            adapter: 'aws'
            options:
                client: 'aws_client_service'
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'

        users.storage:
            adapter: 'cache'
            options:
                store: 'psr6_cache_pool' # A service ID implementing Psr\Cache\CacheItemPoolInterface
                source: 'users.storage.source'
```

However, this configuration is generic. Most of the time you are only
going to need one of two possibilities:

* memory cache: this cache will expire at the end of the current CLI process or 
  HTTP request ;
* persistant cache: this cache will only expire when you clear it ;

#### Memory caching

To enable memory cache, you can create a dedicated service based on the Symfony
Cache component:

```yaml
# config/packages/flysystem.yaml

services:
    users.storage.cache:
        class: 'Symfony\Component\Cache\Adapter\ArrayAdapter'

flysystem:
    storages:
        # ...

        users.storage:
            adapter: 'cache'
            options:
                store: 'users.storage.cache'
                source: 'users.storage.source'
```

#### Persistent caching

To enable persistent cache, you can either define your own service in the same way
as the memory cache, or rely on the `cache.app` service provided automatically by
Symfony:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        # ...

        users.storage:
            adapter: 'cache'
            options:
                store: 'cache.app'
                source: 'users.storage.source'
```
