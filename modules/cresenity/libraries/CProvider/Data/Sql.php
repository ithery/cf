<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 12:55:10 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CProvider_Data_Sql extends CProvider_DataAbstract {

    protected $sql;

    public function setSql($sql) {
        $this->sql = $sql;
        return $this;
    }

    public function getSql($sql) {
        return $this->sql;
    }

    public function data() {
        
    }

}
