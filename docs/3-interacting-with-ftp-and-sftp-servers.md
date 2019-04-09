# Interacting with FTP and SFTP servers

Flysystem is able to interact using the same FilesystemInterface with FTP and SFTP servers.
To configure this bundle for this usage, you can rely on adapters in the same way you would
for other storages.

## FTP

### Installation

The FTP adapter is shipped natively by Flysystem and does not need to be installed.

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
                private_key: 'path/to/or/contents/of/privatekey'
                root: '/path/to/root'
                timeout: 10
```
