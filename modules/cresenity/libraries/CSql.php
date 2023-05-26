<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @deprecated 1.6, use CParser_Sql
 */
class CSql {
    public static function format($query) {
        return CSql_Formatter::format($query, false);
    }

    public static function highlight($query) {
        return CSql_Formatter::highlight($query);
    }
}
