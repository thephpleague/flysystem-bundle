# Getting started

- [Installation](#installation)
- [Basic usage](#basic-usage)
- [Defining multiple filesystems](#defining-multiple-filesystems)

## Installation

flysystem-bundle requires PHP 7.1+ and Symfony 4.2+.

You can install the bundle using Symfony Flex:

```
composer require thephpleague/flysystem-bundle
```

## Basic usage

The default configuration file created by Symfony Flex provides enough configuration to
use Flysystem in your application as soon as you install the bundle:

```yaml
# config/packages/flysystem.yaml

flysystem:
    default_filesystem: 'default.storage'
    filesystems:
        default.storage:
            adapter: 'flysystem.adapter.local'
```

This configuration creates a single filesystem service, configured using the local adapter in the `storge` directory, 
and provides this service for autowiring using the `League\Flysystem\FilesystemInterface` interface. 
This autowiring will target the default filesystem defined in the bundle configuration.
 
This means that if you are using autowiring, you can typehint `League\Flysystem\FilesystemInterface` in any
of your services to get the default filesystem:

```php
use League\Flysystem\FilesystemInterface;

class MyService
{
    private $storage;
    
    public function __construct(FilesystemInterface $storage)
    {
        $this->storage = $storage;
    }
    
    // ...
}
```

The same goes for controllers:

```php
use League\Flysystem\FilesystemInterface;

class MyController
{
    public function index(FilesystemInterface $storage)
    {
        // ...
    }
}
```

If you are not using autowiring, you can inject the `flysystem` service into your services
manually to get the default filesystem.

## Defining multiple filesystems

Using a single filesystem is good way to get up and running quickly, but it is often useful to
create multiple instances of Flysystem in order to manage different filesystems.

This bundle provides this ability using `named aliases`: by leveraging the variable name in addition to
the interface name, autowiring is able to inject the proper filesystem you want in your services.

This means that if you are using autowiring, you can create multiple filesystems in the configuration of the
bundle and typehint `League\Flysystem\FilesystemInterface` with a variable having the same name as your filesystem 
name to get this specific filesystem:

```yaml
# config/packages/flysystem.yaml

flysystem:
    default_filesystem: 'app'
    filesystems:
        upload.storage:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/storage'

        tmp.storage:
            adapter: 'flysystem.adapter.local'
            options:
                directory: '/tmp'
```

```php
use League\Flysystem\FilesystemInterface;

class MyController
{
    public function index(FilesystemInterface $fs, FilesystemInterface $tmpStorage)
    {
        // $fs is referencing the default filesystem ("app") because the variable name is not a filesystem name
        // $tmpStorage is referencing the "tmp.storage" filesystem
    }
}
```
