<?php

class CQC_Testing_AbstractModel extends CModel {
    /**
     * Resolve a connection instance.
     *
     * @param null|string $connection
     *
     * @return CDatabase
     */
    public static function resolveConnection($connection = null) {
        return CQC_Testing_Database::instance()->resolveConnection();
    }
}
