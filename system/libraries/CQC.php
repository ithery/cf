<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
class CQC {
    const TYPE_DATABASE_CHECKER = 'DatabaseChecker';

    const TYPE_UNIT_TEST = 'UnitTest';

    public static function boot() {
        CQC_Bootstrap::boot();
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $group
     *
     * @return void
     */
    public static function registerDatabaseChecker($class, $name = null, $group = null) {
        return CQC_Manager::instance()->registerDatabaseChecker($class, $name, $group);
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $group
     *
     * @return void
     */
    public static function registerUnitTest($class, $name = null, $group = null) {
        return CQC_Manager::instance()->registerUnitTest($class, $name, $group);
    }

    /**
     * @param string $className
     *
     * @return \CQC_Runner_DatabaseCheckerRunner
     */
    public static function createDatabaseCheckerRunner($className) {
        return new CQC_Runner_DatabaseCheckerRunner($className);
    }

    /**
     * @param string $className
     *
     * @return \CQC_Runner_UnitTestRunner
     */
    public static function createUnitTestRunner($className) {
        return new CQC_Runner_UnitTestRunner($className);
    }

    /**
     * @param string $className
     *
     * @return \CQC_Inspector
     */
    public static function createInspector($className) {
        return new CQC_Inspector($className);
    }

    public static function createProcessor($className) {
        $inspector = new CQC_Inspector($className);

        return $inspector->createProcessor();
    }

    public static function cliRunner($className, $parameter = null) {
        $argv = carr::get($_SERVER, 'argv');
        if ($parameter == null) {
            $parameter = $argv[3];
        }
        parse_str($parameter, $options);
        $processor = static::createProcessor($className);
        $processor->run($options);
    }

    /**
     * @return CQC_Manager
     */
    public static function manager() {
        return CQC_Manager::instance();
    }

    /**
     * @return CQC_Executor
     */
    public static function createExecutor() {
        return new CQC_Executor();
    }
}
