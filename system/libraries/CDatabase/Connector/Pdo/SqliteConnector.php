<?php

class CDatabase_Connector_Pdo_SqliteConnector extends CDatabase_Connector implements CDatabase_ConnectorInterface {
    /**
     * Establish a database connection.
     *
     * @param array $config
     *
     * @throws \CDatabase_Exception_SqliteDatabaseDoesNotExistException
     *
     * @return \PDO
     */
    public function connect(array $config) {
        $options = $this->getOptions($config);

        // SQLite supports "in-memory" databases that only last as long as the owning
        // connection does. These are useful for tests or for short lifetime store
        // querying. In-memory databases may only have a single open connection.
        if ($config['database'] === ':memory:') {
            return $this->createConnection('sqlite::memory:', $config, $options);
        }

        $path = realpath($config['database']);

        // Here we'll verify that the SQLite database exists before going any further
        // as the developer probably wants to know if the database exists and this
        // SQLite driver will not throw any exception if it does not by default.
        if ($path === false) {
            throw new CDatabase_Exception_SqliteDatabaseDoesNotExistException($config['database']);
        }

        return $this->createConnection("sqlite:{$path}", $config, $options);
    }
}
