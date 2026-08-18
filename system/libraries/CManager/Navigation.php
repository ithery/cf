<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_Navigation {
    public static function setNavigationCallback(callable $navigationCallback) {
        CApp_Navigation_Data::setNavigationCallback($navigationCallback);
    }

    public static function setAccessCallback(callable $accessCallback) {
        CNavigation::manager()->setAccessCallback($accessCallback);
    }

    public static function setActiveCallback(callable $activeCallback) {
        CNavigation::manager()->setActiveCallback($activeCallback);
    }
}
