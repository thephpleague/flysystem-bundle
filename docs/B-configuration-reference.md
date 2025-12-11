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

        users4.storage:
            adapter: 'gcloud'
            options:
                client: 'gcloud_client_service'
                bucket: 'bucket_name'
                prefix: 'optional/path/prefix'
                
        users5.storage:
            adapter: 'local'
            options:
                directory: '%kernel.project_dir%/storage'
                lock: 0
                skip_links: false
                permissions:
                    file:
                        public: 0o744
                        private: 0o700
                    dir:
                        public: 0o755
                        private: 0o700
            visibility: ~ # default null. Possible values are 'public' or 'private'
            directory_visibility: ~ # default null. Possible values are 'public' or 'private'
            retain_visibility: ~ # default null. When set to `true` or `null`, it will lead to adapters performing visibility checks (e.g. GetObjectAcl command for S3 adapters) on copy and move actions, hence `false` prevents retrieving an object's current visibility and use the value as set in `visibility` instead
            case_sensitive: true
            disable_asserts: false

        users6.storage:
            adapter: 'memory'

        users7.storage:
            adapter: 'null'

        users8.storage:
            adapter: 'sftp'
            options:
                host: 'example.com'
                port: 22
                username: 'username'
                password: 'password'
                privateKey: 'path/to/or/contents/of/privatekey'
                root: '/path/to/root'
                timeout: 10

        users9.storage:
            adapter: 'lazy'
            options:
                source: 'flysystem_storage_service_to_use'

        users10.storage:
            adapter: 'custom_adapter'

        users11.storage:
            adapter: 'local'
            options:
                directory: '/tmp/storage'
            public_url_generator: 'flysystem_public_url_generator_service_to_use'
            temporary_url_generator: 'flysystem_temporary_url_generator_service_to_use'
            read_only: true
```
