<?php

trait CDatabase_Doctrine_Trait_ConnectsToDatabaseTrait {
    /**
     * Create a new database connection.
     *
     * @param mixed[]     $params
     * @param null|string $username
     * @param null|string $password
     * @param mixed[]     $driverOptions
     *
     * @throws \InvalidArgumentException
     *
     * @return \CDatabase_Doctrine_Connection
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = []) {
        if (!isset($params['pdo']) || !$params['pdo'] instanceof PDO) {
            throw new InvalidArgumentException('CF requires the "pdo" property to be set and be a PDO instance.');
        }

        return new CDatabase_Doctrine_Connection($params['pdo']);
    }
}
