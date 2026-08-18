<?php

defined('SYSPATH') or die('No direct access allowed.');

class CImage_Avatar_EngineFactory {
    public static function create($engineName) {
        $className = 'CImage_Avatar_Engine_' . $engineName;

        return new $className();
    }
}
