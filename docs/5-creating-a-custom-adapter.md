# Creating a custom adapter

[Read the associated library documentation](https://flysystem.thephpleague.com/v2/docs/advanced/creating-an-adapter/)

If you have highly specific requirements, you may need to create your own
Flysystem adapter. To do so, you first need to create the adapter code itself
and then use it in your storages configuration.

## Creating the adapter class

A Flysystem adapter is a class implementing the `League\Flysystem\FilesystemAdapter` interface.
To learn all the details about how to create this class, you can read the 
[library documentation](https://flysystem.thephpleague.com/v2/docs/advanced/creating-an-adapter/).

You can create this class everywhere you want in your application. We usually recommend a clear
namespace and class name such as `App\Flysystem\MyCustomAdapter`.

## Using the adapter in a storage configuration

To use the adapter inside your storages configuration, you need to register your newly created
as a service. Fortunately, in most Symfony 4.2+ applications, this is done automatically by Symfony.

> *Note:* if you disabled autodiscovery, you can register manually your adapter as a normal service
> and use the ID your registered instead of the class name in the next YAML examples.

Once created and (automatically) registered as a service, you can reference your adapter inside your
storages:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            adapter: 'App\Flysystem\MyCustomAdapter'
``` 
