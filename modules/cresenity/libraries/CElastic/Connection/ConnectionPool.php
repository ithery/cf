<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 7:50:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Connection_ConnectionPool {

    /**
     * @var array|CElastic_Connection[] Connections array
     */
    protected $_connections;

    /**
     * @var CElastic_Connection_Strategy_StrategyInterface Strategy for connection
     */
    protected $_strategy;

    /**
     * @var callback Function called on connection fail
     */
    protected $_callback;

    /**
     * @param array                                           $connections
     * @param CElastic_Connection_Strategy_StrategyInterface  $strategy
     * @param callback                                        $callback
     */
    public function __construct(array $connections, CElastic_Connection_Strategy_StrategyInterface $strategy, $callback = null) {
        $this->_connections = $connections;
        $this->_strategy = $strategy;
        $this->_callback = $callback;
    }

    /**
     * @param CElastic_Connection $connection
     *
     * @return $this
     */
    public function addConnection(Connection $connection) {
        $this->_connections[] = $connection;
        return $this;
    }

    /**
     * @param array|CElastic_Connection[] $connections
     *
     * @return $this
     */
    public function setConnections(array $connections) {
        $this->_connections = $connections;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasConnection() {
        foreach ($this->_connections as $connection) {
            if ($connection->isEnabled()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getConnections() {
        return $this->_connections;
    }

    /**
     * @throws CElastic_Exeption_ClientException
     *
     * @return CElastic_Connection
     */
    public function getConnection() {
        return $this->_strategy->getConnection($this->getConnections());
    }

    /**
     * @param CElastic_Connection $connection
     * @param \Exception           $e
     * @param CElastic_Client               $client
     */
    public function onFail(CElastic_Connection $connection, Exception $e, CElastic_Client $client) {
        $connection->setEnabled(false);
        if ($this->_callback) {
            call_user_func($this->_callback, $connection, $e, $client);
        }
    }

    /**
     * @return \Elastica\Connection\Strategy\StrategyInterface
     */
    public function getStrategy() {
        return $this->_strategy;
    }

}
