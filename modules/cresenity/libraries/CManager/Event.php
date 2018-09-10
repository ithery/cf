<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 1:46:58 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_Event {

    protected static $appEvent;

    /**
     * 
     * @return CApp_Event
     */
    public static function app() {
        if (self::$appEvent == null) {
            self::$appEvent = new CApp_Event();
        }
        return self::$appEvent;
    }

}
