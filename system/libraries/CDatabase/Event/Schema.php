<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDatabase_Event_Schema {
    /**
     * @var bool
     */
    private $preventDefault = false;

    /**
     * @return CDatabase_Event_Schema
     */
    public function preventDefault() {
        $this->preventDefault = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultPrevented() {
        return $this->preventDefault;
    }
}
