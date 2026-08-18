<?php

use Doctrine\DBAL\Driver\AbstractSQLServerDriver;

class CDatabase_Doctrine_Driver_SqlServerDriver extends AbstractSQLServerDriver {
    /**
     * Create a new database connection.
     *
     * @param mixed[]     $params
     * @param null|string $username
     * @param null|string $password
     * @param mixed[]     $driverOptions
     *
     * @return \CDatabase_Doctrine_SqlServerConnection
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = []) {
        return new CDatabase_Doctrine_SqlServerConnection(
            new CDatabase_Doctrine_Connection($params['pdo'])
        );
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'pdo_sqlsrv';
    }
}
