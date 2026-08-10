# Creating a custom adapter

[Read the associated library documentation](https://flysystem.thephpleague.com/docs/advanced/creating-an-adapter/)

If you have highly specific requirements, you may need to create your own
Flysystem adapter. To do so, you first need to create the adapter code itself
and then use it in your storages configuration.

## Creating the adapter class

A Flysystem adapter is a class implementing the `League\Flysystem\FilesystemAdapter` interface.
To learn all the details about how to create this class, you can read the
[library documentation](https://flysystem.thephpleague.com/docs/advanced/creating-an-adapter/).

You can create this class anywhere you want in your application. We usually recommend a clear
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
            service: 'App\Flysystem\MyCustomAdapter'
```

## Creating a custom adapter builder (advanced)

For more complex custom adapters that require configuration validation, IDE auto-completion,
and integration with the bundle's configuration system, you can create a custom adapter builder.

This allows you to define your custom adapter directly in the configuration:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            my_custom: # Your custom adapter type
                option1: 'value1'
                option2: 'value2'
```

### Creating the adapter builder

Create a class implementing `AdapterDefinitionBuilderInterface`:

```php
<?php

namespace App\Flysystem\Builder;

use App\Flysystem\MyCustomAdapter;
use League\FlysystemBundle\Adapter\Builder\AdapterDefinitionBuilderInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class MyCustomAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'my_custom';
    }

    public function getRequiredPackages(): array
    {
        // Return required packages for your adapter
        // Format: ['ClassName' => 'vendor/package-name']
        return [];
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('option1')
                    ->isRequired()
                    ->info('Description of option1')
                ->end()
                ->scalarNode('option2')
                    ->defaultValue('default_value')
                    ->info('Description of option2')
                ->end()
                ->booleanNode('option3')
                    ->defaultFalse()
                    ->info('Description of option3')
                ->end()
            ->end();
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): string
    {
        $adapterId = 'flysystem.adapter.' . $storageName;

        $definition = new Definition(MyCustomAdapter::class);
        $definition->setPublic(false);

        // Configure your adapter with the options
        $definition->setArgument(0, $options['option1']);
        $definition->setArgument(1, $options['option2']);
        $definition->setArgument(2, $options['option3']);

        $container->setDefinition($adapterId, $definition);

        return $adapterId;
    }
}
```

### Registering the adapter builder

#### Via Bundle (recommended for reusable bundles)

If you're creating a bundle, register your builder in your bundle class:

```php
<?php

namespace App\MyCustomBundle;

use App\Flysystem\Builder\MyCustomAdapterDefinitionBuilder;
use League\FlysystemBundle\FlysystemBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MyCustomBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register your custom adapter builder
        $extension = $container->getExtension('flysystem');
        if ($extension instanceof FlysystemExtension) {
            $extension->addAdapterDefinitionBuilder(new MyCustomAdapterDefinitionBuilder());
        }
    }
}
```

#### Via Kernel (for application-specific adapters)

For application-specific adapters, register your builder in your `Kernel`:

```php
<?php

namespace App;

use App\Flysystem\Builder\MyCustomAdapterDefinitionBuilder;
use League\FlysystemBundle\DependencyInjection\FlysystemExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    // ...

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register your custom adapter builder
        $extension = $container->getExtension('flysystem');
        if ($extension instanceof FlysystemExtension) {
            $extension->addAdapterDefinitionBuilder(new MyCustomAdapterDefinitionBuilder());
        }
    }
}
```

Once registered, you can use the `debug:config flysystem` command to see your custom adapter
and all its available options in the configuration tree.

### Testing your custom builder

Create a test class extending `AbstractAdapterDefinitionBuilderTest`:

```php
<?php

namespace Tests\App\Flysystem\Builder;

use App\Flysystem\Builder\MyCustomAdapterDefinitionBuilder;
use App\Flysystem\MyCustomAdapter;
use League\FlysystemBundle\Test\AbstractAdapterDefinitionBuilderTest;
use Symfony\Component\DependencyInjection\Definition;

class MyCustomAdapterDefinitionBuilderTest extends AbstractAdapterDefinitionBuilderTest
{
    protected function createBuilder(): MyCustomAdapterDefinitionBuilder
    {
        return new MyCustomAdapterDefinitionBuilder();
    }

    public static function provideValidOptions(): \Generator
    {
        yield 'minimal' => [[
            'option1' => 'value1',
        ]];

        yield 'full' => [[
            'option1' => 'value1',
            'option2' => 'custom_value',
            'option3' => true,
        ]];
    }

    protected function assertDefinition(Definition $definition): void
    {
        $this->assertSame(MyCustomAdapter::class, $definition->getClass());
        $this->assertSame('value1', $definition->getArgument(0));
        $this->assertSame('custom_value', $definition->getArgument(1));
        $this->assertTrue($definition->getArgument(2));
    }
}
```

This provides comprehensive testing of your builder's configuration and adapter creation logic.

## Next

[MongoDB GridFS](https://github.com/thephpleague/flysystem-bundle/blob/3.x/docs/6-gridfs.md)
