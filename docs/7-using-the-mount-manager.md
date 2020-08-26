# Using the Mount Manager a custom adapter

[Read the associated library documentation](https://flysystem.thephpleague.com/v1/docs/advanced/mount-manager/)

If you're using multiple filesystems in an application it can be convenient to leverage Flysystem's
[`MountManager`](https://flysystem.thephpleague.com/v1/docs/advanced/mount-manager/) to access different filesystems
from a single object.

## Configuring mount prefixes

Each filesystem you want to include in the `MountManager` must be configured with a `mount_prefix` option:

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        default.storage:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/var/storage/default'
            mount_prefix: 'local'
        s3.storage:
            adapter: 'aws'
            options:
                client: 'aws_client_service' # The service ID of the Aws\S3\S3Client instance
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
          mount_prefix: 's3'
```

(This option is only required if you intend to use this feature.)

## Injecting the Mount Manager

You can then inject the `flysystem.mount_manager` service (or reference the `MountManager` class with autowiring) in your classes to access the `MountManager` and work with your multiple filesystems:

```php
use League\Flysystem\MountManager;

class MyController
{
    public function index(MountManager $manager)
    {
        // Read from local storage
        $contents = $manager->read('local://some/file.txt');

        // And write to an S3 bucket
        $manager->write('s3://put/it/here.txt', $contents);

        // Or better yet, use the specialized "copy" call
        $manager->copy('local://some/file.ext', 's3://put/it/here.txt');
    }
}
```
