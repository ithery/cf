<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 12:55:10 AM
 */
class CProvider_Data_Sql extends CProvider_DataAbstract {
    protected $sql;
    protected $db;

    public function setSql($sql) {
        $this->sql = $sql;
        return $this;
    }

    public function getSql($sql) {
        return $this->sql;
    }

    public function getData() {
        if ($this->db == null) {
            $this->db = CDatabase::instance();
        }
        $row = $this->db->query($this->sql);
        return $row->result_array(false);
    }
}
