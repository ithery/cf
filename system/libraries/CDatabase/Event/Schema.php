<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 12:48:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Event_Schema {

    /**
     * @var bool
     */
    private $_preventDefault = false;

    /**
     * @return CDatabase_Event_Schema
     */
    public function preventDefault() {
        $this->_preventDefault = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultPrevented() {
        return $this->_preventDefault;
    }

}
