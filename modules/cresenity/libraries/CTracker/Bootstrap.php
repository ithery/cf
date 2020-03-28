<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 2:44:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Bootstrap {

    /**
     *
     * @var CTracker_Tracker
     */
    protected $tracker;

    public function __construct() {
        
    }

    public function execute() {
        if (CTracker::config()->get('trackEnabled')) {
            $this->register();
            $this->getTracker()->boot();
        }
    }

    public function config() {
        return CTracker::config();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        if ($this->config()->isTrackEnabled()) {
//            $this->registerAuthentication();
//            $this->registerCache();
//            $this->registerRepositories();
//            $this->registerTracker();
//            $this->registerTablesCommand();
//            $this->registerUpdateGeoIpCommand();
//            $this->registerExecutionCallback();
//            $this->registerUserCheckCallback();
            if ($this->config()->isLogEnabled()) {
                $this->registerSqlQueryLogWatcher();
            }
//            $this->registerGlobalEventLogger();
//            $this->registerDatatables();
//            $this->registerMessageRepository();
//            $this->registerGlobalViewComposers();
        }
    }

    /**
     * @return CTracker_Tracker
     */
    public function getTracker() {
        if (!$this->tracker) {
            $this->tracker = new CTracker_Tracker();
        }
        return $this->tracker;
    }

    protected function registerSqlQueryLogWatcher() {
        if ($this->config()->isLogSqlQuery()) {
            $db = CDatabase::instance($this->config()->get('database'));

            $db->listenOnQueryExecuted(function ($query) use ($db) {
                
                $bindings = $query->bindings;
                $time = $query->time;
                $connection = $query->connection;
                $sql = $query->sql;
                $this->getTracker()->logSqlQuery($sql, $bindings, $time, $connection);
            });
        }
    }

}
