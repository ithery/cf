<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 1:45:25 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Memory {

    protected static $instance;

    public static function instance() {
        if (self::$instance == null) {
            return new CServer_Memory();
        }
        return self::$instance;
    }

}
