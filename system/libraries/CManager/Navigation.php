<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 3, 2018, 6:30:35 PM
 */
class CManager_Navigation {
    public static function setNavigationCallback(callable $navigationCallback) {
        CApp_Navigation_Data::setNavigationCallback($navigationCallback);
    }

    public static function setAccessCallback(callable $accessCallback) {
        CApp_Navigation::setAccessCallback($accessCallback);
    }

    public static function setActiveCallback(callable $activeCallback) {
        CApp_Navigation::setActiveCallback($activeCallback);
    }
}
