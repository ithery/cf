<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 6:10:38 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Log {

    public static function request() {
        return CApp_Log_Request::populate();
    }

    public static function activity($description, $data) {
        return CApp_Log_Activity::populate($description, $data);
    }
   
}
