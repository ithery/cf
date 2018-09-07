<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 3, 2018, 6:30:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_Navigation {

    protected static $navigationCallback;

    public static function setNavigationCallback(callable $navigationCallback) {
        CApp_Navigation_Data::setNavigationCallback($navigationCallback);
    }

}
