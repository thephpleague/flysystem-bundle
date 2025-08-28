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
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 *
 * @internal
 */
final class GridFSAdapterDefinitionBuilder implements AdapterDefinitionBuilderInterface
{
    public function getName(): string
    {
        return 'gridfs';
    }

    public function getRequiredPackages(): array
    {
        return [
            GridFSAdapter::class => 'league/flysystem-gridfs',
        ];
    }

    /**
     * @deprecated since 3.5, use addConfiguration() with the new config format instead
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('bucket')->default(null)->allowedTypes('string', 'null');
        $resolver->define('prefix')->default('')->allowedTypes('string');
        $resolver->define('database')->default(null)->allowedTypes('string', 'null');
        $resolver->define('doctrine_connection')->allowedTypes('string');
        $resolver->define('mongodb_uri')->allowedTypes('string');
        $resolver->define('mongodb_uri_options')->default([])->allowedTypes('array');
        $resolver->define('mongodb_driver_options')->default([])->allowedTypes('array');
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('bucket')
                    ->defaultNull()
                    ->info('GridFS bucket service name (if using an existing bucket service)')
                ->end()
                ->scalarNode('prefix')
                    ->defaultValue('')
                    ->info('Optional path prefix to prepend to all file names')
                ->end()
                ->scalarNode('database')
                    ->defaultNull()
                    ->info('MongoDB database name')
                ->end()
                ->scalarNode('doctrine_connection')
                    ->info('Doctrine MongoDB connection name (mutually exclusive with mongodb_uri)')
                ->end()
                ->scalarNode('mongodb_uri')
                    ->info('MongoDB connection URI (mutually exclusive with doctrine_connection)')
                ->end()
                ->arrayNode('mongodb_uri_options')
                    ->defaultValue([])
                    ->prototype('variable')
                    ->end()
                    ->info('MongoDB URI options')
                ->end()
                ->arrayNode('mongodb_driver_options')
                    ->defaultValue([])
                    ->prototype('variable')
                    ->end()
                    ->info('MongoDB driver options')
                ->end()
            ->end()
        ;
    }

    public function createAdapter(ContainerBuilder $container, string $storageName, array $options, ?string $defaultVisibilityForDirectories): ?string
    {
        $adapterId = 'flysystem.adapter.'.$storageName;

        // Create bucket service based on configuration
        if (isset($options['doctrine_connection'])) {
            if (isset($options['mongodb_uri'])) {
                throw new InvalidArgumentException('In GridFS configuration, "doctrine_connection" and "mongodb_uri" options cannot be set together.');
            }
            $bucket = new Definition(Bucket::class);
            $bucket->setFactory([self::class, 'initializeBucketFromDocumentManager']);
            $bucket->setArguments([
                new Reference(sprintf('doctrine_mongodb.odm.%s_document_manager', $options['doctrine_connection'])),
                $options['database'] ?? null,
                $options['bucket'] ?? null,
            ]);
        } elseif (isset($options['mongodb_uri'])) {
            $bucket = new Definition(Bucket::class);
            $bucket->setFactory([self::class, 'initializeBucketFromConfig']);
            $bucket->setArguments([
                $options['mongodb_uri'],
                $options['mongodb_uri_options'] ?? [],
                $options['mongodb_driver_options'] ?? [],
                $options['database'] ?? throw new InvalidArgumentException('MongoDB "database" name is required for Flysystem GridFS configuration'),
                $options['bucket'] ?? null,
            ]);
        } elseif (isset($options['bucket'])) {
            $bucket = new Reference($options['bucket']);
        } else {
            throw new InvalidArgumentException('Flysystem GridFS configuration requires a "bucket" service name, a "mongodb_uri" or a "doctrine_connection" name');
        }

        $container
            ->setDefinition($adapterId, new Definition(GridFSAdapter::class))
            ->setArgument(0, $bucket)
            ->setArgument(1, $options['prefix'] ?? '');

        return $adapterId;
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
