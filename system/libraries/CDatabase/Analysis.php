<?php

class CDatabase_Analysis {
    protected static $instance;

    private $connection;

    /**
     * @return CDatabase_Analysis
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CDatabase_Analysis();
        }

        return static::$instance;
    }

    public function getConnection() {
        if ($this->connection == null) {
            $this->connection = c::db();
        }

        return $this->connection;
    }

    public function setConnection($connection) {
        if (!$connection instanceof CDatabase_Connection) {
            $connection = c::db($connection);
        }
        $this->connection = $connection;

        return $this;
    }

    public function getExplainer($query) {
        $db = $this->getConnection();

        $explainQuery = (strpos(strtolower($query), 'explain') === false ? 'EXPLAIN ' : '') . $query;
        $result = $db->select($explainQuery);

        return new CDatabase_Analysis_Explainer($db, $result);
    }
}
