# Cloud storage providers

One of the core features of Flysystem is its ability to interact easily with remote filesystems,
including many cloud storage providers. This bundle provides the same level of support for these
cloud providers by providing corresponding adapters in the configuration.

* [Azure](#azure)
* [AsyncAws S3](#asyncaws-s3)
* [AWS SDK S3](#aws-sdk-s3)
* [Google Cloud Storage](#google-cloud-storage)
* [DigitalOcean Spaces](#digitalocean-spaces)
* [Scaleway Object Storage](#scaleway-object-storage)
* [Cloudflare R2](#cloudflare-r2)

## Azure

> [!WARNING]
> The built-in `azure` adapter (based on `league/flysystem-azure-blob-storage`) is **deprecated**
> since 3.8, as the underlying package is abandoned. Use
> [`php-oss-for-azure/azure-storage-blob-flysystem-bundle-php`](https://github.com/php-oss-for-azure/azure-storage-blob-flysystem-bundle-php)
> instead, which provides its own adapter that plugs directly into this bundle.

### Installation

```
composer require league/flysystem-azure-blob-storage
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            azure:
                client: 'azure_client_service' # The service ID of the MicrosoftAzure\Storage\Blob\BlobRestProxy instance
                container: 'container_name'
                prefix: 'optional/path/prefix'
```

## AsyncAws S3

### Installation

```
composer require league/flysystem-async-aws-s3
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            asyncaws:
                client: 'aws_client_service' # The service ID of the AsyncAws\S3\S3Client instance
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
                mimeTypeDetector: ~ # e.g. App\Flysystem\MyeMimeTypeDetector
                forwardedOptions: ['ACL', 'CacheControl', 'Metadata'] # AWS options to forward to the client, defaults to AsyncAwsS3Adapter::AVAILABLE_OPTIONS
```

## AWS SDK S3

### Installation

```
composer require league/flysystem-aws-s3-v3
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            # visibility: public # Make the uploaded file publicly accessible in S3
            aws:
                client: 'aws_client_service' # The service ID of the Aws\S3\S3Client instance
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
                streamReads: true
                mimeTypeDetector: ~ # e.g. App\Flysystem\MyMimeTypeDetector
                forwardedOptions: ['ACL', 'CacheControl', 'Metadata'] # AWS options to forward to the client, defaults to AwsS3V3Adapter::AVAILABLE_OPTIONS
```

## Google Cloud Storage

### Installation

```
composer require league/flysystem-google-cloud-storage
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            gcloud:
                client: 'gcloud_client_service' # The service ID of the Google\Cloud\Storage\StorageClient instance
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
                streamReads: false
                mimeTypeDetector: ~ # e.g. App\Flysystem\MyMimeTypeDetector
```

## DigitalOcean Spaces

The DigitalOcean Spaces is compatible with the AWS S3 API, meaning that you can use the same configuration
as for an AWS storage. For example:

```yaml
# config/packages/flysystem.yaml

services:
    digitalocean_spaces_client:
        class: AsyncAws\S3\S3Client
        arguments:
            -
                endpoint: '%env(DIGITALOCEAN_SPACES_ENDPOINT)%'
                accessKeyId: '%env(DIGITALOCEAN_SPACES_ID)%'
                accessKeySecret: '%env(DIGITALOCEAN_SPACES_SECRET)%'

flysystem:
    storages:
        cdn.storage:
            asyncaws:
                client: 'digitalocean_spaces_client'
                bucket: '%env(DIGITALOCEAN_SPACES_BUCKET)%'
```

## Scaleway Object Storage

The Scaleway Object Storage is compatible with the AWS S3 API, meaning that you can use the same configuration
as for an AWS storage. For example:

```yaml
# config/packages/flysystem.yaml

services:
    scaleway_spaces_client:
        class: AsyncAws\S3\S3Client
        arguments:
            -
                endpoint: '%env(SCALEWAY_SPACES_ENDPOINT)%'
                accessKeyId: '%env(SCALEWAY_SPACES_ID)%'
                accessKeySecret: '%env(SCALEWAY_SPACES_SECRET)%'

flysystem:
    storages:
        cdn.storage:
            asyncaws:
                client: 'scaleway_spaces_client'
                bucket: '%env(SCALEWAY_SPACES_BUCKET)%'
```

## Cloudflare R2

The Cloudflare R2 is compatible with the AWS S3 API, meaning that you can use the same configuration
as for an AWS storage. Both the regular and the async AWS Client can be used. For example:

```yaml
# config/packages/flysystem.yaml

services:
    cloudflare_r2_client:
        class: AsyncAws\S3\S3Client
        arguments:
            -
                endpoint: '%env(CLOUDFLARE_R2_ENDPOINT)%'
                accessKeyId: '%env(CLOUDFLARE_R2_ID)%'
                accessKeySecret: '%env(CLOUDFLARE_R2_SECRET)%'

flysystem:
    storages:
        cdn.storage:
            asyncaws:
                client: 'cloudflare_r2_client'
                bucket: '%env(CLOUDFLARE_R2_BUCKET)%'
```

Cloudflare R2 does not implement ACL-related features yet, so using Flysystem's `move` and `copy` methods requires
setting an explicit `visibility` value and setting `retain_visibility` to `false`, to prevent the S3 adapter from
calling the unsupported `GetObjectAcl` command to retrieve an object's current ACL visibility (which would otherwise
result in an exception).

```yaml
flysystem:
    storages:
        cdn.storage:
            # ...
            visibility: private # or public

            # to use the visibility as defined above instead of retaining the object's visibility, and not having to run
            # the unsupported `GetObjectAcl` command to get the object's current visibility.
            retain_visibility: false
            # ...
```

## Next

[Interacting with FTP and SFTP servers](https://github.com/thephpleague/flysystem-bundle/blob/3.x/docs/3-interacting-with-ftp-and-sftp-servers.md)
