<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 2:18:08 AM
 */
final class CQueue {
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
        foreach (['Null', 'Sync', 'Database', 'Redis', 'AsyncRedis', 'Beanstalkd', 'Sqs'] as $connector) {
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
            return false;
        };

        return new CQueue_Worker(static::queuer(), CEvent::dispatcher(), CException::exceptionHandler(), $isDownForMaintenance);
    }

    public static function run($connection = null) {
        $runner = new CQueue_Runner(CQueue::worker(), null);
        $runner->run($connection);
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
}
