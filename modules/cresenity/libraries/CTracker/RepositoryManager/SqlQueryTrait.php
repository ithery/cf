<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:31:29 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTracker_RepositoryManager_SqlQueryTrait {

    /**
     *
     * @var CTracker_Repository_Log
     */
    protected $sqlQueryRepository;

    protected function bootSqlQueryTrait() {
        $this->sqlQueryRepository = new CTracker_Repository_SqlQuery();
    }

    public function logSqlQuery($query, $bindings, $time, $name) {
        $this->sqlQueryRepository->push([
            'query' => $query,
            'bindings' => $bindings,
            'time' => $time,
            'name' => $name,
        ]);
    }

}
