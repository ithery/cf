<?php

defined('SYSPATH') or die('No direct access allowed.');
//@codingStandardsIgnoreStart
class cmsg {
    public static function add($type, $message) {
        return CApp_Message::add($type, $message);
    }

    public static function get($type) {
        return CApp_Message::get($type);
    }

    public static function clear($type) {
        return CApp_Message::clear($type);
    }

    public static function clear_all() {
        return CApp_Message::clearAll();
    }

    public static function flash($type) {
        return CApp_Message::flash($type);
    }

    public static function flash_all() {
        return CApp_Message::flashAll();
    }
}
 //@codingStandardsIgnoreEnd
