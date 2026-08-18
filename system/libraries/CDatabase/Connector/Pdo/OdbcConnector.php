<?php

class CDatabase_Connector_Pdo_OdbcConnector extends CDatabase_Connector implements CDatabase_ConnectorInterface {
    /**
     * @throws \Exception
     */
    public function connect(array $config): PDO {
        return $this->createConnection(
            carr::get($config, 'dsn'),
            $config,
            $this->getOptions($config),
        );
    }
}
