<?php

class CManager_DataProvider_SqlDataProvider extends CManager_DataProviderAbstract {
    protected $connection = '';

    protected $sql;

    protected $bindings;

    public function __construct($sql, $bindings = []) {
        $this->sql = $sql;
        $this->bindings = $bindings;
    }

    public function setConnection($connection) {
        $this->connection = $connection;
    }

    public function getConnection() {
        return $this->connection ?: 'default';
    }

    public function toEnumerable() {
        return c::db($this->connection)->query($this->sql, $this->bindings);
    }
}
