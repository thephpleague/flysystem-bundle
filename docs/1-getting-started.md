# Getting started

- [Installation](#installation)
- [Basic usage](#basic-usage)
- [Using multiple storages to improve readability](#using-multiple-storages-to-improve-readability)
- [Using memory storage in tests](#using-memory-storage-in-tests)

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

* either using autowiring, by typehinting against the `FilesystemOperator` and using the
  variable name matching one of your storages:

    ```php
    use League\Flysystem\FilesystemOperator;
    
    class MyService
    {
        private $storage;
        
        // The variable name $defaultStorage matters: it needs to be the camelized version
        // of the name of your storage. 
        public function __construct(FilesystemOperator $defaultStorage)
        {
            $this->storage = $defaultStorage;
        }
        
        // ...
    }
    ```
    
  The same goes for controllers:
    
    ```php
    use League\Flysystem\FilesystemOperator;
    
    class MyController
    {
        // The variable name $defaultStorage matters: it needs to be the camelized version
        // of the name of your storage. 
        public function index(FilesystemOperator $defaultStorage)
        {
            // ...
        }
    }
    ```

* or using manual injection, by injecting the service named `default.storage` inside 
  your services.
  
Once you have a FilesystemOperator, you can call methods from the
[Filesystem API](https://flysystem.thephpleague.com/v2/docs/usage/filesystem-api/)
to interact with your storage.


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
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/storage/users'
                
        projects.storage:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/storage/projects'
``` 

```php
use League\Flysystem\FilesystemOperator;

class MyService
{
    private $usersStorage;
    private $projectsStorage;
    
    public function __construct(FilesystemOperator $usersStorage, FilesystemOperator $projectsStorage)
    {
        $this->usersStorage = $usersStorage;
        $this->projectsStorage = $projectsStorage;
    }
    
    // ...
}
```


## Using memory storage in tests

One of the best reason to use a filesystem abstraction in your project is the ability
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
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/storage/users'
``` 

```yaml
# config/packages/test/flysystem.yaml

flysystem:
    storages:
        users.storage:
            adapter: 'memory'
```

This configuration will swap every reference to the `users.storage` service (or to the
`FilesystemOperator $usersStorage` typehint) from a local adapter to a memory one during tests.

## Using read only to disallow any write operations

In some context, it can be useful to protect any write operations on your storages service.

To achieve this, you need to install the read-only package :

```
composer require league/flysystem-read-only
```

And then, you can configure your storage with the `readonly` options.

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/storage/users'
            readonly: true
```

With this configuration, any write operation will throw a suitable exception.

## Next

[Cloud storage providers](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/2-cloud-storage-providers.md)
