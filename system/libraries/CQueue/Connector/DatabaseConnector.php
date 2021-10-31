<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 4:11:18 AM
 */
class CQueue_Connector_DatabaseConnector extends CQueue_AbstractConnector {
    /**
     * Database connections.
     *
     * @var CDatabase_ResolverInterface
     */
    protected $connections;

    /**
     * Create a new connector instance.
     *
     * @param CDatabase_ResolverInterface $db
     *
     * @return void
     */
    public function __construct(CDatabase_ResolverInterface $connections = null) {
        if ($connections == null) {
            $connections = CDatabase_Resolver::instance();
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
