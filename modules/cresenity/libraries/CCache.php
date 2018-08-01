<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CCache {

    public static function manager() {
        return new CCache_Manager();
    }

}
