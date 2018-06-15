<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:51:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CHelper {

    /**
     * 
     * @return \CHelper_File
     */
    public static function file() {
        return new CHelper_File();
    }

    public static function formatter() {
        return new CHelper_Formatter();
    }

   

}
