<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 2, 2019, 11:01:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Database {

    /**
     *
     * @var CDatabase
     */
    protected $db;

    /**
     * 
     * @param CDatabase $db
     * @return $this
     */
    public function setDatabase(CDatabase $db) {
        $this->db = $db;
        return $this;
    }

    /**
     * 
     * @return CDatabase
     */
    public function db() {
        if ($this->db == null) {
            return CDatabase::instance();
        }
        return $this->db;
    }

}
