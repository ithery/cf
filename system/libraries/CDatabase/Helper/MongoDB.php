<?php

class CDatabase_Helper_MongoDB {
    public static function commandToString($commands) {
        return 'db.runCommand(' . json_encode($commands) . ')';
    }

    public static function stringToCommand() {
    }
}
