<?php

class CQC_Testing_Config {
    protected $regexFileMatcher = '"/([A-Za-z0-9\\/._-]+)(?::| on line )([1-9][0-9]*)/"';

    protected static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function getRegexFileMatcher() {
        return $this->regexFileMatcher;
    }
}
