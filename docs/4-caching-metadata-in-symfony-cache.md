# Caching metadata in Symfony cache

[Read the associated library documentation](https://flysystem.thephpleague.com/docs/advanced/caching/)

Networking and I/O are often the source of performance problems in applications.
On top of this, Flysystem generally aims to be as reliable as possible, which 
means it will often check and validate operations before they are done. This
involves an additional overhead on top of the classical slowness of I/O.

When your application needs to scale, you may need to cache metadata in order to
improve performances on this level. To do this, you can use the `cache` adapter.

> *Note:* this adapter caches anything but the file content. This keeps the cache 
> small enough to be beneficial and covers all the file system inspection operations.

### Installation

```
composer require league/flysystem-cached-adapter
```

### Usage

The cache adapter works using a source storage (from which it will read the uncached data)
and a [Symfony cache pool](https://symfony.com/doc/current/reference/configuration/framework.html#pools). 

Most of the time you are only going to need one of two possibilities:

* in-memory cache (will expire at the end of the CLI process or HTTP request) ;
* persistant cache (will stay persistent in a cache backend) ;

#### Memory caching

To use in-memory caching, you can create a dedicated Symfony cache pool:

```yaml
# config/packages/flysystem.yaml

framework:
    cache:
        pools:
            cache.users.storage:
                adapter: cache.adapter.array
                default_lifetime: 3600

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
                store: 'cache.users.storage'
                source: 'users.storage.source'
```

#### Persistent caching

To use persistent caching, you can either create a dedicated Symfony cache pool
or use the pool `cache.app` which is defined by default by Symfony:

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
                store: 'cache.app'
                source: 'users.storage.source'
```

## Next

[Creating a custom adapter](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/5-creating-a-custom-adapter.md)
