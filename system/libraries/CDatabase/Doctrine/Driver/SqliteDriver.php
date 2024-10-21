<?php

use Doctrine\DBAL\Driver\AbstractSQLiteDriver;

class CDatabase_Doctrine_Driver_SqliteDriver extends AbstractSQLiteDriver {
    use CDatabase_Doctrine_Trait_ConnectsToDatabaseTrait;

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'pdo_sqlite';
    }
}
