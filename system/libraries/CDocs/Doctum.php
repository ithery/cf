<?php

class CDocs_Doctum {
    private static $instance;

    /**
     * @return CDocs_Doctum
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
