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
            ftp:
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
                # Force the transfer mode instead of letting the adapter detect it
                # (FTP_ASCII or FTP_BINARY constant on the ftp extension)
                transfer_mode: ~
                # Force the FTP server system type instead of letting the adapter detect it
                system_type: ~ # 'windows' or 'unix'
                timestamps_on_unix_listings_enabled: false
                recurse_manually: true
                use_raw_list_options: ~
                # Service ID of a League\Flysystem\Ftp\ConnectionProvider implementation
                connectionProvider: ~ # e.g. 'App\Flysystem\MyConnectionProvider'
                # Service ID of a League\Flysystem\Ftp\ConnectivityChecker implementation
                connectivityChecker: ~ # e.g. 'App\Flysystem\MyConnectivityChecker'
                mimeTypeDetector: ~ # e.g. App\Flysystem\MyMimeTypeDetector
                # Detect the mime type using the file path instead of its content
                detect_mime_type_using_path: false
                permissions:
                    file:
                        public: 0o644
                        private: 0o600
                    dir:
                        public: 0o755
                        private: 0o700
```

## SFTP

### Installation

```
composer require league/flysystem-sftp-v3
```

### Usage

```yaml
# config/packages/flysystem.yaml

flysystem:
    storages:
        backup.storage:
            sftp:
                host: 'example.com'
                port: 22
                username: 'username'
                password: 'password'
                privateKey: 'path/to/or/contents/of/privatekey'
                passphrase: 'privatekey_passphrase'
                # Use the SSH agent for authentication instead of a password/private key
                useAgent: false
                # A single fingerprint or a list of accepted fingerprints
                hostFingerprint: 'host_fingerprint' # or ['host_fingerprint_1', 'host_fingerprint_2']
                preferredAlgorithms:
                    hostkey: ['rsa-sha2-256', 'ssh-rsa']
                root: '/path/to/root'
                timeout: 10
                # Maximum number of connection attempts
                maxTries: 4
                mimeTypeDetector: ~ # e.g. App\Flysystem\MyMimeTypeDetector
                # Detect the mime type using the file path instead of its content
                detect_mime_type_using_path: false
                # Disconnect the SFTP connection when the adapter is destructed
                disconnect_on_destruct: false
                permissions:
                    file:
                        public: 0o644
                        private: 0o600
                    dir:
                        public: 0o755
                        private: 0o700
```

## Next

[Using a lazy adapter to switch storage backend using an environment variable](https://github.com/thephpleague/flysystem-bundle/blob/3.x/docs/4-using-lazy-adapter-to-switch-at-runtime.md)
