# Cloud storage providers

One of the core feature of Flysystem is its ability to interact easily with remote filesystems,
including many cloud storage providers. This bundle provides the same level of support for these
cloud providers by providing corresponding adapters in the configuration.

* [AsyncAws S3](#asyncaws-s3)
* [AWS S3](#aws-s3)
* [DigitalOcean Spaces](#digitalocean-spaces)
* [Scaleway Object Storage](#scaleway-object-storage)

## AsyncAws S3

### Installation

```
composer require async-aws/flysystem-s3
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

## AWS S3

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

## DigitalOcean Spaces

The DigitalOcean Spaces are compatible with the AWS S3 API, meaning that you can use the same configuration
as for a AWS storage.

## Scaleway Object Storage

The Scaleway Object Storage is compatible with the AWS S3 API, meaning that you can use the same configuration
as for a AWS storage.

## Next

[Interacting with FTP and SFTP servers](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/3-interacting-with-ftp-and-sftp-servers.md)
