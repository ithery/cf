<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Log {
    public static function request() {
        return CApp_Log_Request::populate();
    }

    public static function activity($description, $data) {
        return CApp_Log_Activity::populate($description, $data);
    }
}
