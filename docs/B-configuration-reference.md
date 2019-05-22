# Configuration reference

```yaml
flysystem:
    storages:
        users1.storage:
            adapter: 'aws'
            options:
                client: 'aws_client_service'
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'

        users2.storage:
            adapter: 'azure'
            options:
                client: 'azure_client_service'
                container: 'container_name'
                prefix: 'optional/path/prefix'

        users3.storage:
            adapter: 'cache'
            options:
                store: 'cache.app'
                source: 'fs_local'

        users4.storage:
            adapter: 'custom_adapter'

        users5.storage:
            adapter: 'dropbox'
            options:
                client: 'dropbox_client_service'
                prefix: 'optional/path/prefix'

        users6.storage:
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

        users7.storage:
            adapter: 'gcloud'
            options:
                client: 'gcloud_client_service'
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
                api_url: 'https://storage.googleapis.com'

        users8.storage:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/storage'
                lock: 0
                skip_links: false
                permissions:
                    file:
                        public: 0744
                        private: 0700
                    dir:
                        public: 0755
                        private: 0700

        users9.storage:
            adapter: 'memory'

        users10.storage:
            adapter: 'null'

        users11.storage:
            adapter: 'rackspace'
            options:
                container: 'rackspace_container_service'
                prefix: 'optional/path/prefix'

        users12.storage:
            adapter: 'replicate'
            options:
                source: 'fs_aws'
                replica: 'fs_local'

        users13.storage:
            adapter: 'sftp'
            options:
                host: 'example.com'
                port: 22
                username: 'username'
                password: 'password'
                private_key: 'path/to/or/contents/of/privatekey'
                root: '/path/to/root'
                timeout: 10

        users14.storage:
            adapter: 'webdav'
            options:
                client: 'webdav_client_service'
                prefix: 'optional/path/prefix'
                use_stream_copy: false

        users15.storage:
            adapter: 'zip'
            options:
                path: '%kernel.project_dir%/archive.zip'
```
