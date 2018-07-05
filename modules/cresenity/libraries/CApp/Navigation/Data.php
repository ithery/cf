<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 1, 2018, 12:57:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Navigation_Data {

    public static function get($domain = null) {
        $navFile = CF::get_file('config', 'nav', $domain);

        $data = null;
        if ($navFile != null) {
            $data = include $navFile;
        }
        return $data;
    }

}
