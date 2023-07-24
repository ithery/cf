<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Project {
    /**
     * @param type $type
     *
     * @return \CApp_Project_AbstractGenerator
     */
    private static function generator($type) {
        $className = 'CApp_Project_Generator_' . $type . 'Generator';

        return new $className();
    }

    /**
     * @return CApp_Project_Generator_ModelGenerator
     */
    public static function modelGenerator() {
        return static::generator('Model');
    }
}
