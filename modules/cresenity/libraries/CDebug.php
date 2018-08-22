<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 1:03:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDebug {

    protected static $bar;

    /**
     * 
     * @param array $options
     * @return CDebug_Bar
     */
    public static function bar($options = array()) {
        if (self::$bar == null) {
            self::$bar = new CDebug_Bar($options);
        } else {
            self::$bar->setOptions($options);
        }
        return self::$bar;
    }

}
