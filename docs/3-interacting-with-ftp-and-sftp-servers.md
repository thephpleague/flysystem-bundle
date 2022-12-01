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
composer require league/flysystem-sftp-v3
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

### Sample

```
<?php
final class ProductsExporter
{
    /**
     * Constructor
     *
     * @param FilesystemOperator $someExportCsvStorage
     */
    public function __construct(
        protected FilesystemOperator $someExportCsvStorage,
    ) {
    }

    /**
     * Exports to CSV and save it to the SFTP flysystem
     *
     * @param OutputInterface $output
     *
     * @return bool
     * @throws InvalidArgument
     * @throws CannotInsertRecord
     * @throws Exception
     * @throws FilesystemException
     */
    public function export(OutputInterface $output): bool
    {
        $date = Carbon::now()
                      ->format('Y-m-d');
        $output->write("Exporting ... ");

        $writer = Writer::createFromPath('php://temp');
        $writer->setDelimiter(';');
        $writer->setOutputBOM(ByteSequence::BOM_UTF8);
        // force "" enclosure as it allows us to just open the file in Excel
        EncloseField::addTo($writer, "\t\x1f");
        $writer->insertOne([
                               'sku',
                               'name',
                               'description',
                               'meta_title',
                               'meta_keyword',
                               'meta_description',
                           ]);


        $this->someExportCsvStorage->write(
            'products.' . $date . '.csv',
            $writer->toString()
        );
        $output->writeln("Done.");


        return true;
    }
}
```

## Next

[Using a lazy adapter to switch storage backend using an environment variable](https://github.com/thephpleague/flysystem-bundle/blob/master/docs/4-using-lazy-adapter-to-switch-at-runtime.md)
