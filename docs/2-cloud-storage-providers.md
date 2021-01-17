# Cloud storage providers

One of the core feature of Flysystem is its ability to interact easily with remote filesystems,
including many cloud storage providers. This bundle provides the same level of support for these
cloud providers by providing corresponding adapters in the configuration.

* [AsyncAws S3](#asyncaws-s3)
* [AWS S3](#aws-sdk-s3)
* [DigitalOcean Spaces](#digitalocean-spaces)
* [Scaleway Object Storage](#scaleway-object-storage)
* [Google Cloud Storage](#google-cloud-storage)

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
            adapter: 'asyncaws'
            options:
                client: 'aws_client_service' # The service ID of the AsyncAws\S3\S3Client instance
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
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
            adapter: 'aws'
            # visibility: public # Make the uploaded file publicly accessible in S3
            options:
                client: 'aws_client_service' # The service ID of the Aws\S3\S3Client instance
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
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
            adapter: 'gcloud'
            options:
                client: 'gcloud_client_service' # The service ID of the Google\Cloud\Storage\StorageClient instance
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
```

## DigitalOcean Spaces

The DigitalOcean Spaces are compatible with the AWS S3 API, meaning that you can use the same configuration
as for a AWS storage. For example:

```yaml
# config/packages/flysystem.yaml

services:
    digitalocean_spaces_client:
        class: 'AsyncAws\S3\S3Client'
        arguments:
            -  endpoint: '%env(DIGITALOCEAN_SPACES_ENDPOINT)%'
               accessKeyId: '%env(DIGITALOCEAN_SPACES_ID)%'
               accessKeySecret: '%env(DIGITALOCEAN_SPACES_SECRET)%'

flysystem:
    storages:
        cdn.storage:
            adapter: 'asyncaws'
            options:
                client: 'digitalocean_spaces_client'
                bucket: '%env(DIGITALOCEAN_SPACES_BUCKET)%'
```

## Scaleway Object Storage

The Scaleway Object Storage is compatible with the AWS S3 API, meaning that you can use the same configuration
as for a AWS storage. For example:

```yaml
# config/packages/flysystem.yaml

services:
    scaleway_spaces_client:
        class: 'AsyncAws\S3\S3Client'
        arguments:
            -  endpoint: '%env(SCALEWAY_SPACES_ENDPOINT)%'
               accessKeyId: '%env(SCALEWAY_SPACES_ID)%'
               accessKeySecret: '%env(SCALEWAY_SPACES_SECRET)%'

flysystem:
    storages:
        cdn.storage:
            adapter: 'asyncaws'
            options:
                client: 'scaleway_spaces_client'
                bucket: '%env(SCALEWAY_SPACES_BUCKET)%'
```

## Next

[Interacting with FTP and SFTP servers](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/3-interacting-with-ftp-and-sftp-servers.md)
