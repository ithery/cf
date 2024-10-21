<?php

class CParser_Sql {
    public static function format($query) {
        return CParser_Sql_SqlFormatter::format($query, false);
    }

    public static function highlight($query) {
        return CParser_Sql_SqlFormatter::highlight($query);
    }
}
