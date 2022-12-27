<?php

class CQC_Bootstrap {
    protected static $booted = false;

    public static function boot() {
        if (!static::$booted) {
            //boot logic
            static::$booted = true;
        }
    }
}
