<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 12, 2019, 3:29:17 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Project {

    /**
     * 
     * @param type $type
     * @return \CApp_Project_AbstractGenerator
     */
    private static function generator($type) {
        $className = 'CApp_Project_Generator_' . $type . 'Generator';
        return new $className;
    }

    /**
     * 
     * @return CApp_Project_Generator_ModelGenerator
     */
    public static function modelGenerator() {
        return static::generator('Model');
    }

}
