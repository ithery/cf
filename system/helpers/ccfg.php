<?php

//@codingStandardsIgnoreStart
class ccfg {
    /**
     * Undocumented function
     *
     * @param [type] $name
     * @param [type] $appCode
     *
     * @return void
     *
     * @deprecated 1.1
     */
    public static function get_data($name, $appCode = null) {
        return CApp_Config::getData($name, $appCode);
    }

    public static function get($key, $domain = '') {
        return CApp_Config::get($key, $domain);
    }
}
//@codingStandardsIgnoreEnd