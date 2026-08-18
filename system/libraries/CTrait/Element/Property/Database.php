<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Property_Database {
    /**
     * @var CDatabase_Connection
     */
    protected $db;

    /**
     * @param CDatabase_Connection $db
     *
     * @return $this
     */
    public function setDatabase(CDatabase_Connection $db) {
        $this->db = $db;

        return $this;
    }

    /**
     * @return CDatabase_Connection
     */
    public function db() {
        if ($this->db == null) {
            return c::db();
        }

        return $this->db;
    }
}
