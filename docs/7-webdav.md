# WebDAV 

Flysystem is able to [interact with WebDAV servers](https://flysystem.thephpleague.com/docs/adapter/webdav/).
To configure this bundle for such usage, you can rely on adapters in the same way you would
for other storages.

### Installation

```
composer require league/flysystem-webdav
```

### Usage

```yaml
# config/packages/flysystem.yaml

services:
  webdav_client:
    class: Sabre\DAV\Client
    arguments:
      - { baseUri: 'https://webdav.example.com/', userName: 'your_user', password: 'superSecret1234' }

flysystem:
    storages:
        webdav.storage:
            adapter: 'webdav'
            options:
                client: 'webdav_client'
                prefix: 'optional/path/prefix'
                visibility_handling: !php/const \League\Flysystem\WebDAV\WebDAVAdapter::ON_VISIBILITY_THROW_ERROR # throw
                manual_copy: false
                manual_move: false
```
