<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Connector_DatabaseConnector extends CQueue_AbstractConnector {
    /**
     * Database connections.
     *
     * @var CDatabase_Manager
     */
    protected $connections;

    /**
     * Create a new connector instance.
     *
     * @param CDatabase_Contract_ConnectionResolverInterface $connections
     *
     * @return void
     */
    public function __construct(CDatabase_Contract_ConnectionResolverInterface $connections = null) {
        if ($connections == null) {
            $connections = CDatabase_Manager::instance();
        }
        $this->connections = $connections;
    }

    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return CQueue_AbstractQueue
     */
    public function connect(array $config) {
        //todo read from config
        return new CQueue_Queue_DatabaseQueue(
            $this->connections->connection(carr::get($config, 'connection')),
            carr::get($config, 'table'),
            carr::get($config, 'queue'),
            carr::get($config, 'retry_after', 60)
        );
    }
}
