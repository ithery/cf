<?php

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;

class CDatabase_Doctrine_Driver_PostgresDriver extends AbstractPostgreSQLDriver {
    use CDatabase_Doctrine_Trait_ConnectsToDatabaseTrait;

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'pdo_pgsql';
    }
}
