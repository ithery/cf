<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 2:18:08 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CQueue {

    protected static $dispatcher;

    /**
     * 
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

    public static function queuer() {
        return CF::tap(new CQueue_Manager(), function ($manager) {
                    CQueue::registerConnectors($manager);
                });
    }

    /**
     * Register the connectors on the queue manager.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    public static function registerConnectors($manager) {
//        foreach (['Null', 'Sync', 'Database', 'Redis', 'Beanstalkd', 'Sqs'] as $connector) {
//            self::{"register{$connector}Connector"}($manager);
//        }
        foreach (['Null', 'Database'] as $connector) {
            self::{"register{$connector}Connector"}($manager);
        }
    }

    /**
     * Register the Null queue connector.
     *
     * @param  CQueue_Manager  $manager
     * @return void
     */
    protected static function registerNullConnector($manager) {
        $manager->addConnector('null', function () {
            return new CQueue_Connector_NullConnector;
        });
    }

    /**
     * Register the Sync queue connector.
     *
     * @param  CQueue_Manager  $manager
     * @return void
     */
    protected static function registerSyncConnector($manager) {
        $manager->addConnector('sync', function () {
            return new CQueue_Connector_SyncConnector;
        });
    }

    /**
     * Register the database queue connector.
     *
     * @param  CQueue_Manager  $manager
     * @return void
     */
    protected static function registerDatabaseConnector($manager) {
        $manager->addConnector('database', function () {
            return new CQueue_Connector_DatabaseConnector(CDatabase::instance());
        });
    }

    public static function worker() {
        $isDownForMaintenance = function () {
            return false;
        };
        return new CQueue_Worker(static::queuer(), CEvent::dispatcher(), CException::createExceptionHandler(), $isDownForMaintenance);
    }

    public static function run() {

        $runner = new CQueue_Runner(CQueue::worker(), null);
        $runner->run();
    }
    
}
