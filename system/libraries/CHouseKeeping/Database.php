<?php

class CHouseKeeping_Database {
    public static function cleanLogActivity($keepDays = 365) {
        return CHouseKeeping_Database_LogActivity::execute($keepDays);
    }
}
