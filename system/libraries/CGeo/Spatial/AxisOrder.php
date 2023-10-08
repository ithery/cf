<?php

class CGeo_Spatial_AxisOrder {
    public function __construct() {
    }

    public function supported(CDatabase_Connection $connection) {
        /** @var CDatabase_Connection_Pdo_MySqlConnection $connection */
        if ($this->isMariaDb($connection)) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        if ($this->isMySql57($connection)) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }

    private function isMariaDb(CDatabase_Connection_Pdo_MySqlConnection $connection) {
        return $connection->isMaria();
    }

    private function isMySql57(CDatabase_Connection_Pdo_MySqlConnection $connection) {
        /** @var string $version */
        $version = $connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        return version_compare($version, '5.8.0', '<');
    }
}
