<?php

/**
 * @deprecated
 */
class ccfg {

    public static function get_data($name, $appCode = null) {
        return CApp_Config::getData($name, $appCode);
    }

    public static function get($key, $domain = "") {
        return CApp_Config::get($key, $domain);
    }

}
