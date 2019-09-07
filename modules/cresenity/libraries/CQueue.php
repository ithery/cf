<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 2:18:08 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CQueue {

    protected static $dispatcher;

    public static function dispatcher() {
        if (self::$dispatcher == null) {
            self::$dispatcher = new CQueue_Dispatcher(CContainer::getInstance());
        }
        return self::$dispatcher;
    }

}
