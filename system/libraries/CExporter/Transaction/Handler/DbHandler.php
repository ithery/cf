<?php

class CExporter_Transaction_Handler_DbHandler implements CExporter_Transaction_HandlerInterface {
    /**
     * @var CDatabase_Connection
     */
    private $connection;

    /**
     * @param CDatabase_Connection $connection
     */
    public function __construct(CDatabase_Connection $connection) {
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
