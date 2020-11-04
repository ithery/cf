<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Oct 7, 2020 
 * @license Ittron Global Teknologi
 */
class CQC {

    /**
     * 
     * @param string $class
     * @param string $name
     * @param string $group
     * @return void
     */
    public static function registerDatabaseChecker($class, $name = null, $group = null) {
        return CQC_Manager::instance()->registerDatabaseChecker($class, $name, $group);
    }

    /**
     * 
     * @param string $class
     * @param string $name
     * @param string $group
     * @return void
     */
    public static function registerUnitTest($class, $name = null, $group = null) {
        return CQC_Manager::instance()->registerUnitTest($class, $name, $group);
    }

    /**
     * 
     * @param string $className
     * @return \CQC_Runner_DatabaseCheckerRunner
     */
    public static function createDatabaseCheckerRunner($className) {
        return new CQC_Runner_DatabaseCheckerRunner($className);
    }

}
