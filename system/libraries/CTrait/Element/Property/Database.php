<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 2, 2019, 11:01:13 PM
 */
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
     * @return CDatabase
     */
    public function db() {
        if ($this->db == null) {
            return c::db();
        }

        return $this->db;
    }
}
