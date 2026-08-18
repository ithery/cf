<?php

use Doctrine\DBAL\Driver\AbstractMySQLDriver;

class CDatabase_Doctrine_Driver_MySqlDriver extends AbstractMySQLDriver {
    use CDatabase_Doctrine_Trait_ConnectsToDatabaseTrait;

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'pdo_mysql';
    }
}
