<?php

interface CDatabase_ConnectorInterface {
    /**
     * Establish a database connection.
     *
     * @param array $config
     *
     * @return \PDO
     */
    public function connect(array $config);
}
