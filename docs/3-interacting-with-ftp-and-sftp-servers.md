# Interacting with FTP and SFTP servers

Flysystem is able to interact with FTP and SFTP servers using the same FilesystemOperator.
To configure this bundle for such usage, you can rely on adapters in the same way you would
for other storages.

## FTP

### Installation

```
composer require league/flysystem-ftp
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        backup.storage:
            adapter: 'ftp'
            options:
                host: 'ftp.example.com'
                username: 'username'
                password: 'password'
                port: 21
                root: '/path/to/root'
                passive: true
                ssl: true
                timeout: 30
                ignore_passive_address: ~
                utf8: false
```

## SFTP

### Installation

```
composer require league/flysystem-sftp
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        backup.storage:
            adapter: 'sftp'
            options:
                host: 'example.com'
                port: 22
                username: 'username'
                password: 'password'
                privateKey: 'path/to/or/contents/of/privatekey'
                root: '/path/to/root'
                timeout: 10
                directoryPerm: 0744
                permPublic: 0700
                permPrivate: 0744
```

## Next

[Using a lazy adapter to switch storage backend using an environment variable](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/4-using-lazy-adapter-to-switch-at-runtime.md)
