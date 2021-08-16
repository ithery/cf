<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 1:46:58 PM
 */
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
