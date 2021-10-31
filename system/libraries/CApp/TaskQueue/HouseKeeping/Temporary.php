<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 2:24:45 AM
 */
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
