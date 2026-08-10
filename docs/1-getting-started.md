# Getting started

- [Installation](#installation)
- [Basic usage](#basic-usage)
- [Transferring files with console commands](#transferring-files-with-console-commands)
- [Using multiple storages to improve readability](#using-multiple-storages-to-improve-readability)
- [Using memory storage in tests](#using-memory-storage-in-tests)
- [Using read only to disallow any write operations](#using-read-only-to-disallow-any-write-operations)
- [Storage options](#storage-options)

## Installation

flysystem-bundle requires PHP 8.2+ and Symfony 6.0+.

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
            local:
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
use Symfony\Component\DependencyInjection\Attribute\Target;

class MyService
{
    public function __construct(
        #[Target('default.storage')] private FilesystemOperator $storage,
    ) {
    }

    // ...
}
```

**2) Using manual service registration:** in your services, inject the storage service
directly using its configured name (in this case `default.storage`):

```yaml
# config/services.yaml
services:
    # ...

    App\MyService:
        arguments:
            $storage: '@default.storage'
```

Once you have a FilesystemOperator, you can call methods from the
[Filesystem API](https://flysystem.thephpleague.com/docs/usage/filesystem-api/)
to interact with your storage.

## Transferring files with console commands

If you need to transfer files between the local filesystem and one of your configured
storages, the bundle also provides two console commands:

```bash
bin/console flysystem:push <storage> <local-source> [remote-destination]
bin/console flysystem:pull <storage> <remote-source> [local-destination]
```

The `<storage>` argument is the configured Flysystem storage name (for example
`default.storage`), not the adapter type. When the destination is omitted, the basename
of the source path is used.

## Using multiple storages to improve readability

While using the default storage can be enough, it is usually recommended to create multiple
storages, even if behind the scene you may rely on the same adapter.

The reason for this is the added readability this provides to your project code: by naming
your storages using their **intents**, you will naturally increase the readability of your
autowired arguments. For example:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            local:
                directory: '%kernel.project_dir%/storage/users'

        projects.storage:
            local:
                directory: '%kernel.project_dir%/storage/projects'
```

```php
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Target;

class MyService
{
    public function __construct(
        #[Target('users.storage')] private FilesystemOperator $usersStorage,
        #[Target('projects.storage')] private FilesystemOperator $projectsStorage,
    ) {
    }

    // ...
}
```

## Using memory storage in tests

One of the best reasons to use a filesystem abstraction in your project is the ability
it gives you to swap the actual implementation during tests.

More specifically, it can be useful to swap from a persisted storage to a memory one during
tests, both to ensure the state is reset between tests and to increase tests speed.

To achieve this, you need to install the memory provider:

```
composer require league/flysystem-memory
```

Then, you can overwrite your storages in the test environment:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            local:
                directory: '%kernel.project_dir%/storage/users'
```

```yaml
# config/packages/test/flysystem.yaml

flysystem:
    storages:
        users.storage:
            memory: ~
```

This configuration will swap every reference to the `users.storage` service (or to a
`#[Target('users.storage')] FilesystemOperator` argument) from a local adapter to a memory one
during tests.

## Using read only to disallow any write operations

In some contexts, it can be useful to protect any write operations on your storages service.

To achieve this, you need to install the read-only package:

```
composer require league/flysystem-read-only
```

And then, you can configure your storage with the `read_only` option.

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            local:
                directory: '%kernel.project_dir%/storage/users'
            read_only: true
```

With this configuration, any write operation will throw a suitable exception.

## Storage options

Beyond the adapter-specific options, every storage accepts a set of general options that
are passed to the underlying Flysystem `Filesystem` instance, regardless of the adapter used:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            local:
                directory: '%kernel.project_dir%/storage/users'

            # Default visibility (public/private) applied to files that don't specify one
            visibility: public

            # Default visibility (public/private) applied to directories that don't specify one
            directory_visibility: public

            # Keep the original file visibility when copying or moving it, instead of
            # recomputing it from the defaults above
            retain_visibility: true

            # Base URL(s) used to build public URLs for adapters that can't generate one
            # natively (a list of URLs is sharded across paths using a hash of the path)
            public_url: 'https://cdn.example.com/'

            # Service ID of a League\Flysystem\PathNormalizer implementation, to override
            # how paths are normalized before being sent to the adapter
            path_normalizer: 'App\Flysystem\MyPathNormalizer'

            # Service ID of a League\Flysystem\UrlGeneration\PublicUrlGenerator
            # implementation, used instead of the adapter/public_url logic
            public_url_generator: 'App\Flysystem\MyPublicUrlGenerator'

            # Service ID of a League\Flysystem\UrlGeneration\TemporaryUrlGenerator
            # implementation, required to call $storage->temporaryUrl() on adapters that
            # don't support it natively (AWS S3, AsyncAws S3, Azure and Google Cloud
            # Storage do support it out of the box)
            temporary_url_generator: 'App\Flysystem\MyTemporaryUrlGenerator'
```

All of these options are optional: adapters that natively support public/temporary URLs
(such as AWS S3, AsyncAws S3, Azure Blob Storage, Google Cloud Storage and WebDAV for
public URLs) don't require `public_url` or `public_url_generator` to be configured.

## Next

[Cloud storage providers](https://github.com/thephpleague/flysystem-bundle/blob/3.x/docs/2-cloud-storage-providers.md)
