<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_TaskQueue_HouseKeeping_Temporary extends CApp_TaskQueue_HouseKeeping {
    public function execute() {
        echo static::class;
    }

    public function getJobId() {
        return '1';
    }

    public function getRawBody() {
        return 'A';
    }
}
