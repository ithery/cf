<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Log {
    /**
     * @return bool|void
     */
    public static function request() {
        return CApp_Log_Request::populate();
    }

    /**
     * @param string $description
     * @param array  $data
     *
     * @return void
     */
    public static function activity($description, $data) {
        return CApp_Log_Activity::populate($description, $data);
    }
}
