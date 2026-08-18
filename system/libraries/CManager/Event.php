<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_Event {
    protected static $appEvent;

    /**
     * @return CApp_Event
     */
    public static function app() {
        if (self::$appEvent == null) {
            self::$appEvent = new CApp_Event();
        }

        return self::$appEvent;
    }
}
