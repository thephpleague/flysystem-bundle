# MongoDB GridFS

GridFS stores files in a MongoDB database.

Install the GridFS adapter:

```
composer require league/flysystem-gridfs
```

## With `doctrine/mongodb-odm-bundle`

For applications that uses Doctrine MongoDB ODM, set the `doctrine_connection` name to use:    

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            adapter: 'gridfs'
            options:
                # Name of a Doctrine MongoDB ODM connection
                doctrine_connection: 'default'
                # Use the default DB from the Doctrine MongoDB ODM configuration
                database: ~
                bucket: 'fs'
```

## With a Full Configuration

To initialize the GridFS bucket from configuration, set the `mongodb_uri` and `database` options, others are optional.

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        users.storage:
            adapter: 'gridfs'
            options:
                # MongoDB client configuration
                mongodb_uri: '%env(MONGODB_URI)%'
                mongodb_uri_options: []
                mongodb_driver_options: []
                # Database name is required
                database: '%env(MONGODB_DB)%'
                bucket: 'fs'
```

```dotenv
# .env

MONGODB_URI=mongodb://127.0.0.1:27017/
MONGODB_DB=flysystem
```

## With a Bucket Service

For a more advanced configuration, create a service for
[`MongoDB\GridFS\Bucket`](https://www.mongodb.com/docs/php-library/current/tutorial/gridfs/):

```yaml
# config/packages/flysystem.yaml

services:
    mongodb_client:
        class: 'MongoDB\Client'
        arguments: ['%env(MONGODB_URI)%']

    mongodb_database:
        class: 'MongoDB\Database'
        factory: ['@mongodb_client', 'selectDatabase']
        arguments: ['%env(MONGODB_DB)%']

    mongodb_gridfs_bucket:
        class: 'MongoDB\GridFS\Bucket'
        factory: ['@mongodb_database', 'selectGridFSBucket']

flysystem:
    storages:
        users.storage:
            adapter: 'gridfs'
            options:
                # Service name
                bucket: 'mongodb_gridfs_bucket'
```
