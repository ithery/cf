<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:32:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_SqlQuery extends CTracker_AbstractRepository {

    use CModel_Tracker_TrackerSqlQueryTrait;
    
    private $queries = [];

    /**
     * @var CTracker_Repository_SqlQueryLog
     */
    private $sqlQueryLogRepository;

    /**
     * @var CTracker_Repository_SqlQueryBinding
     */
    private $sqlQueryBindingRepository;

    /**
     * @var CTracker_Repository_SqlQueryBindingParameter
     */
    private $sqlQueryBindingParameterRepository;

    /**
     * @var CTracker_Repository_Connection
     */
    private $connectionRepository;

    /**
     * @var CTracker_Repository_Log
     */
    private $logRepository;

    /**
     * @var CTracker_Config
     */
    private $config;

    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryModel', 'CTracker_Model_SqlQuery');
        $this->createModel();
        $this->refererParser = new CTracker_Parser_RefererParser();
        $this->currentUrl = curl::current(true);

        parent::__construct();

        $this->sqlQueryLogRepository = new CTracker_Repository_SqlQueryLog();
        $this->sqlQueryBindingRepository = new CTracker_Repository_SqlQueryBinding();
        $this->sqlQueryBindingParameterRepository = new CTracker_Repository_SqlQueryBindingParameter();
        $this->connectionRepository = new CTracker_Repository_Connection();
        $this->logRepository = new CTracker_Repository_Log();
        $this->config = CTracker::config();
    }

    public function fire() {
        if (!$this->logRepository->getCurrentLogId()) {
            return;
        }
        foreach ($this->queries as $query) {
            $this->logQuery($query);
        }
        $this->clear();
    }

    private function sqlQueryIsLoggable($sqlQuery) {
        return strpos($sqlQuery, '"tracker_') === false;
    }

    private function serializeBindings($bindings) {
        return serialize($bindings);
    }

    public function push($query) {
        $this->queries[] = $query;
        $this->fire();
    }

    private function logQuery($query) {
        $sqlQuery = htmlentities($query['query']);
        $bindings = $query['bindings'];
        $time = $query['time'];
        $name = $query['name'];
        if (!$this->sqlQueryIsLoggable($sqlQuery)) {
            return;
        }
        $connectionId = $this->connectionRepository->findOrCreate(
                ['name' => $name], ['name']
        );
        $sqlQueryId = $this->findOrCreate(
                [
            'sha1' => sha1($sqlQuery),
            'statement' => $sqlQuery,
            'time' => $time,
            'log_connection_id' => $connectionId,
                ], ['sha1']
        );
        if ($bindings && $this->canLogBindings()) {
            $bindingsSerialized = $this->serializeBindings($bindings);
            $sqlQueryBindingId = $this->sqlQueryBindingRepository->findOrCreate(['sha1' => sha1($bindingsSerialized), 'serialized' => $bindingsSerialized], ['sha1'], $created);
            if ($created) {
                foreach ($bindings as $parameter => $value) {
                    $this->sqlQueryBindingParameterRepository->create([
                        'log_sql_query_binding_id' => $sqlQueryBindingId,
                        // unfortunately laravel uses question marks,
                        // but hopefully someday this will change
                        'name' => '?',
                        'value' => $value,
                    ]);
                }
            }
        }
        $this->sqlQueryLogRepository->create([
            'log_log_id' => $this->logRepository->getCurrentLogId(),
            'log_sql_query_id' => $sqlQueryId,
        ]);
    }

    private function canLogBindings() {
        return $this->config->get('log_sql_queries_bindings');
    }

    /**
     * @return array
     */
    private function clear() {
        return $this->queries = [];
    }

}
