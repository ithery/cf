<?php

class CExporter_Transaction_Handler_DbHandler implements CExporter_Transaction_HandlerInterface {
    /**
     * @var CDatabase
     */
    private $connection;

    /**
     * @param CDatabase $connection
     */
    public function __construct(CDatabase $connection) {
        $this->connection = $connection;
    }

    /**
     * @param $callback
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function __invoke($callback) {
        return $this->connection->transaction($callback);
    }
}
