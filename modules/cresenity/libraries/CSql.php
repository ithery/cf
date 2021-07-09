<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 4:36:16 PM
 */
class CSql {
    public static function format($query) {
        return CSql_Formatter::format($query, false);
    }

    public static function highlight($query) {
        return CSql_Formatter::highlight($query);
    }
}
