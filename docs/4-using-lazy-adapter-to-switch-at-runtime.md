# Using a lazy adapter to switch storage backend using an environment variable

One of the main reason why using a filesystem abstraction is useful is because
you can switch the storage backend depending on the execution environment
(local files in development, cloud storage in production and memory in tests).

The classical way of doing this would be to create a 
`config/packages/dev/flysystem.yaml` file that would override the configuration
defined in `config/packages/flysystem.yaml`, thus creating a different storage 
service for each environment.

Using this technique is recommended in most cases: by declaring your services
statically and relying on the environment to choose which one to use, you will
profit from the performance optimizations done by the Symfony container to remove
unused services.

However, this technique is not always flexible enough. What if you need
one staging production environment with local storage and the real production
environment with cloud storage?

In such cases, you need to choose the adapter to use at runtime instead of
compile time. To do so, you can use a `lazy` adapter.

A `lazy` adapter is a "fake" adapter that will delay the creation of the actual
storage at runtime. This will allow you to change the storage to create dynamically,
for instance using an environment variable.

Let's take a real-world example:

```yaml
# config/packages/flysystem.yaml

services:
    Aws\S3\S3Client:
        arguments:
            - version: '2006-03-01'
              region: 'fr-par'
              credentials:
                  key: '%env(S3_STORAGE_KEY)%'
                  secret: '%env(S3_STORAGE_SECRET)%'

flysystem:
    storages:
        uploads.storage.aws:
            adapter: 'aws'
            options:
                client: 'Aws\S3\S3Client'
                bucket: 'my-bucket'
                prefix: '%env(S3_STORAGE_PREFIX)%'

        uploads.storage.local:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/var/storage/uploads'

        uploads.storage.memory:
            adapter: 'memory'

        uploads.storage:
            adapter: 'lazy'
            options:
                source: '%env(APP_UPLOADS_SOURCE)%'
```

In this example, we define 3 actual storages: aws, local and memory. They
have a `uploads.storage.` prefix to note their relationship with `uploads.storage`
but this prefix does not have any impact on the behavior itself.

By using a `lazy` adapter for `uploads.storage`, we are able to provide a source
which will be actually used when the service will need to be instantiated. In this case,
we rely on an environment variable to choose the actual storage to use.

This technique allows us to specify a different storage to use in different environments:

```
# To use the AWS storage
APP_UPLOADS_SOURCE=uploads.storage.aws

# To use the local storage
APP_UPLOADS_SOURCE=uploads.storage.local

# To use the memory storage
APP_UPLOADS_SOURCE=uploads.storage.memory 
```

Other than being created at runtime, the `lazy` adapter is behaving in the exact
same way as any other storage:

* you can use it with autowiring, by typehinting against the `FilesystemOperator` and using the
  variable name matching its name:

    ```php
    use League\Flysystem\FilesystemOperator;
    
    class MyService
    {
        private $storage;
        
        public function __construct(FilesystemOperator $uploadsStorage)
        {
            $this->storage = $uploadsStorage;
        }
        
        // ...
    }
    ```

* you can use it in manual injection by injecting the service named `uploads.storage` inside 
  your services. 

## Next

[Creating a custom adapter](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/5-creating-a-custom-adapter.md)
