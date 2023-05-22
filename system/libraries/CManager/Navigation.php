<?php

defined('SYSPATH') or die('No direct access allowed.');

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
