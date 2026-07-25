<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_TaskQueue_HouseKeeping_Temporary extends CApp_TaskQueue_HouseKeeping {
    /**
     * @return void
     */
    public function execute() {
        echo static::class;
    }

    /**
     * @return string
     */
    public function getJobId() {
        return '1';
    }

    /**
     * @return string
     */
    public function getRawBody() {
        return 'A';
    }
}
