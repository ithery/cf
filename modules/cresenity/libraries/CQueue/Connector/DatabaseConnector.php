<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 4:11:18 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CQueue_Connector_DatabaseConnector extends CQueue_AbstractConnector {

    /**
     * Database connections.
     *
     * @var CDatabase
     */
    protected $db;

    /**
     * Create a new connector instance.
     *
     * @param  CDatabase  $db
     * @return void
     */
    public function __construct(CDatabase $db) {
        $this->db = $db;
    }

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return CQueue_QueueInterface
     */
    public function connect(array $config) {
        //todo read from config
        return new CQueue_Queue_DatabaseQueue($this->db, CQueue::config('table'));
    }

}
