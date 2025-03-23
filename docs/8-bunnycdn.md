# BunnyCDN Storage

Flysystem is able to [interact with BunnyCDN Storage servers](https://bunny.net/storage/).
To configure this bundle for such usage, you can rely on adapters in the same way you would
for other storages.

### Installation

```
composer require platformcommunity/flysystem-bunnycdn
```

### Usage

```yaml
# config/packages/flysystem.yaml

services:
  bunny_client:
    class: PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNClient
    arguments:
      $storage_zone_name: 'storage-zone'
      $api_key: 'api-key'
      $region: '!php/const:PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNRegion::FALKENSTEIN'

flysystem:
    storages:
        bunny.storage:
            adapter: 'bunnycdn'
            options:
                client: 'bunny_client'
                pull_zone: 'https://testing.b-cdn.net/' # optional
```
