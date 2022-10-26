<?php

defined('SYSPATH') or die('No direct access allowed.');

use Aws\DynamoDb\DynamoDbClient;

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 2:18:08 AM
 */
final class CQueue {
    protected static $isUsingRedisSupervisor = false;

    /**
     * @var CQueue_Dispatcher
     */
    protected static $dispatcher;

    /**
     * @var CQueue_BatchRepository
     */
    protected static $batchRepository;

    /**
     * @var CQueue_Manager
     */
    protected static $queueManager;

    /**
     * @var CQueue_BatchFactory
     */
    protected static $batchFactory;

    /**
     * @var CQueue_AbstractFailedJob
     */
    protected static $failer;

    protected static $runner;

    /**
     * @return CQueue_Dispatcher
     */
    public static function dispatcher() {
        if (self::$dispatcher == null) {
            self::$dispatcher = new CQueue_Dispatcher(CContainer::getInstance(), function ($connection = null) {
                return CQueue::queuer()->connection($connection);
            });
        }

        return self::$dispatcher;
    }

    /**
     * @return CQueue_Manager
     */
    public static function queuer() {
        if (static::$queueManager == null) {
            static::$queueManager = c::tap(new CQueue_Manager(), function ($manager) {
                CQueue::registerConnectors($manager);
            });
        }

        return static::$queueManager;
    }

    /**
     * Register the connectors on the queue manager.
     *
     * @param CQueue_Manager $manager
     *
     * @return void
     */
    public static function registerConnectors($manager) {
        // foreach (['Null', 'Sync', 'Database', 'Redis', 'Beanstalkd', 'Sqs'] as $connector) {
        //     self::{"register{$connector}Connector"}($manager);
        // }
        foreach (['Null', 'Sync', 'Database', 'Redis', 'AsyncRedis', 'Beanstalkd', 'Sqs', 'File'] as $connector) {
            self::{"register{$connector}Connector"}($manager);
        }
    }

    /**
     * Register the Null queue connector.
     *
     * @param CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerNullConnector($manager) {
        $manager->addConnector('null', function () {
            return new CQueue_Connector_NullConnector();
        });
    }

    /**
     * Register the File queue connector.
     *
     * @param CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerFileConnector($manager) {
        $manager->addConnector('file', function () {
            return new CQueue_Connector_FileConnector();
        });
    }

    /**
     * Register the Sync queue connector.
     *
     * @param CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerSyncConnector($manager) {
        $manager->addConnector('sync', function () {
            return new CQueue_Connector_SyncConnector();
        });
    }

    /**
     * Register the Sync queue connector.
     *
     * @param CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerBeanstalkdConnector($manager) {
        $manager->addConnector('beanstalkd', function () {
            return new CQueue_Connector_BeanstalkdConnector();
        });
    }

    /**
     * Register the database queue connector.
     *
     * @param CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerDatabaseConnector($manager) {
        $manager->addConnector('database', function () {
            return new CQueue_Connector_DatabaseConnector();
        });
    }

    /**
     * Register the Amazon SQS queue connector.
     *
     * @param \CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerSqsConnector($manager) {
        $manager->addConnector('sqs', function () {
            return new CQueue_Connector_SqsConnector();
        });
    }

    /**
     * Register the Redis queue connector.
     *
     * @param \CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerRedisConnector($manager) {
        $manager->addConnector('redis', function () {
            return new CQueue_Connector_RedisConnector(CRedis::instance());
        });
    }

    /**
     * Register the Redis queue connector.
     *
     * @param \CQueue_Manager $manager
     *
     * @return void
     */
    protected static function registerAsyncRedisConnector($manager) {
        $manager->addConnector('async-redis', function () {
            return new CQueue_Connector_AsyncRedisConnector(CRedis::instance());
        });
    }

    public static function worker() {
        $isDownForMaintenance = function () {
            return CF::isDownForMaintenance();
        };

        return new CQueue_Worker(static::queuer(), CEvent::dispatcher(), CException::exceptionHandler(), $isDownForMaintenance);
    }

    public static function run($connection = null, array $options = []) {
        static::$runner = new CQueue_Runner(CQueue::worker(), null, $options);
        static::$runner->run($connection);
        static::$runner = null;
    }

    /**
     * @return null|CQueue_Runner
     */
    public static function runner() {
        return static::$runner;
    }

    public static function config($config, $default = null) {
        return CF::config('queue.' . $config, $default);
    }

    public static function primaryKey($database, $table) {
        return $database->driverName() == 'MongoDB' ? '_id' : $table . '_id';
    }

    public static function batchFactory() {
        if (static::$batchFactory == null) {
            static::$batchFactory = new CQueue_BatchFactory(static::queuer());
        }

        return static::$batchFactory;
    }

    public static function batchRepository() {
        if (static::$batchRepository == null) {
            static::$batchRepository = new CQueue_BatchRepository(
                static::batchFactory(),
                CDatabase::instance(CF::config('queue.batching.database')),
                CF::config('queue.batching.table', 'queue_batch')
            );
        }

        return static::$batchRepository;
    }

    public static function failer() {
        if (static::$failer == null) {
            static::$failer = static::resolveFailer();
        }

        return static::$failer;
    }

    protected static function resolveFailer() {
        $config = CF::config('queue.failed');

        if (isset($config['driver']) && $config['driver'] === 'dynamodb') {
            return static::createDynamoFailedJobProvider($config);
        } elseif (isset($config['driver']) && $config['driver'] === 'database-uuids') {
            return static::createDatabaseUuidFailedJobProvider($config);
        } elseif (isset($config['table'])) {
            return static::createDatabaseFailedJobProvider($config);
        } else {
            return new CQueue_FailedJob_NullFailedJob();
        }
    }

    /**
     * Create a new database failed job provider.
     *
     * @param array $config
     *
     * @return \CQueue_FailedJob_DatabaseFailedJob
     */
    protected static function createDatabaseFailedJobProvider($config) {
        return new CQueue_FailedJob_DatabaseFailedJob(
            c::db(),
            $config['table']
        );
    }

    /**
     * Create a new database failed job provider that uses UUIDs as IDs.
     *
     * @param array $config
     *
     * @return \CQueue_FailedJob_DatabaseUuidFailedJob
     */
    protected static function createDatabaseUuidFailedJobProvider($config) {
        return new CQueue_FailedJob_DatabaseUuidFailedJob(
            c::db(),
            $config['table']
        );
    }

    /**
     * Create a new DynamoDb failed job provider.
     *
     * @param array $config
     *
     * @return \CQueue_FailedJob_DynamoDbFailedJob
     */
    protected static function createDynamoFailedJobProvider($config) {
        $dynamoConfig = [
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => isset($config['endpoint']) ? $config['endpoint'] : null,
        ];

        if (!empty($config['key']) && !empty($config['secret'])) {
            $dynamoConfig['credentials'] = carr::only(
                $config,
                ['key', 'secret', 'token']
            );
        }

        return new CQueue_FailedJob_DynamoDbFailedJob(
            new DynamoDbClient($dynamoConfig),
            CF::config('app.name'),
            $config['table']
        );
    }
}
