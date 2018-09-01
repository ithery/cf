<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 11:38:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript {

    protected static $jQuerys = array();

    public static function createJQuery() {
        $jQuery = new CJavascript_JQuery();

        self::$jQuerys[] = &$jQuery;
        return $jQuery;
    }

    public static function compile() {
        $script = '';
        foreach (self::$jQuerys as $jQuery) {
            $script .= $jQuery->compile();
        }
        return $script;
    }

}
