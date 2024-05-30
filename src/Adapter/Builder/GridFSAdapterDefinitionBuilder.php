<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\Adapter\Builder;

use Doctrine\ODM\MongoDB\DocumentManager;
use League\Flysystem\GridFS\GridFSAdapter;
use MongoDB\Client;
use MongoDB\GridFS\Bucket;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 *
 * @internal
 */
final class GridFSAdapterDefinitionBuilder extends AbstractAdapterDefinitionBuilder
{
    public function getName(): string
    {
        return 'gridfs';
    }

    protected function getRequiredPackages(): array
    {
        return [
            GridFSAdapter::class => 'league/flysystem-gridfs',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('bucket')->default(null)->allowedTypes('string', 'null');
        $resolver->define('prefix')->default('')->allowedTypes('string');
        $resolver->define('database')->default(null)->allowedTypes('string', 'null');
        $resolver->define('doctrine_connection')->allowedTypes('string');
        $resolver->define('mongodb_uri')->allowedTypes('string');
        $resolver->define('mongodb_uri_options')->default([])->allowedTypes('array');
        $resolver->define('mongodb_driver_options')->default([])->allowedTypes('array');
    }

    /**
     * @param array{bucket:string|null, prefix:string, database:string|null, doctrine_connection?:string, mongodb_uri?:string, mongodb_uri_options:array, mongodb_driver_options:array} $options
     */
    protected function configureDefinition(Definition $definition, array $options, ?string $defaultVisibilityForDirectories): void
    {
        if (isset($options['doctrine_connection'])) {
            if (isset($options['mongodb_uri'])) {
                throw new InvalidArgumentException('In GridFS configuration, "doctrine_connection" and "mongodb_uri" options cannot be set together.');
            }
            $bucket = new Definition(Bucket::class);
            $bucket->setFactory([self::class, 'initializeBucketFromDocumentManager']);
            $bucket->setArguments([
                new Reference(sprintf('doctrine_mongodb.odm.%s_document_manager', $options['doctrine_connection'])),
                $options['database'],
                $options['bucket'],
            ]);
        } elseif (isset($options['mongodb_uri'])) {
            $bucket = new Definition(Bucket::class);
            $bucket->setFactory([self::class, 'initializeBucketFromConfig']);
            $bucket->setArguments([
                $options['mongodb_uri'],
                $options['mongodb_uri_options'],
                $options['mongodb_driver_options'],
                $options['database'] ?? throw new InvalidArgumentException('MongoDB "database" name is required for Flysystem GridFS configuration'),
                $options['bucket'],
            ]);
        } elseif ($options['bucket']) {
            $bucket = new Reference($options['bucket']);
        } else {
            throw new InvalidArgumentException('Flysystem GridFS configuration requires a "bucket" service name, a "mongodb_uri" or a "doctrine_connection" name');
        }

        $definition->setClass(GridFSAdapter::class);
        $definition->setArgument(0, $bucket);
        $definition->setArgument(1, $options['prefix']);
    }

    public static function initializeBucketFromDocumentManager(DocumentManager $documentManager, ?string $dbName, ?string $bucketName): Bucket
    {
        return $documentManager
            ->getClient()
            ->selectDatabase($dbName ?? $documentManager->getConfiguration()->getDefaultDB())
            ->selectGridFSBucket(['bucketName' => $bucketName ?? 'fs', 'disableMD5' => true]);
    }

    public static function initializeBucketFromConfig(string $uri, array $uriOptions, array $driverOptions, ?string $dbName, ?string $bucketName): Bucket
    {
        return (new Client($uri, $uriOptions, $driverOptions))
            ->selectDatabase($dbName)
            ->selectGridFSBucket(['bucketName' => $bucketName ?? 'fs', 'disableMD5' => true]);
    }
}
