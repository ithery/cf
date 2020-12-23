<?php

defined('SYSPATH') or die('No direct access allowed.');

class CView {
    /**
     * Hint path delimiter value.
     *
     * @var string
     */
    const HINT_PATH_DELIMITER = '::';

    /**
     * Default view folder.
     *
     * @var string
     */
    const VIEW_FOLDER = 'views';

    /**
     * Creates a new CView using the given parameters.
     *
     * @param null|mixed $name
     * @param mixed      $data
     * @param mixed      $mergeData
     *
     * @return object
     */
    public static function factory($name = null, $data = [], $mergeData = []) {
        if ($name == null) {
            return CView_Factory::instance();
        }
        return CView_Factory::instance()->make($name, $data);
        //return new CView($name, $data, $type);
    }

    /**
     * Check a CView is exists.
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function exists($name) {
        return CView_Factory::instance()->exists($name);
    }

    public static function blade() {
        return CView_Compiler_BladeCompiler::instance();
    }

    public static function finder() {
        return CView_Finder::instance();
    }

    public static function engineResolver() {
        return CView_EngineResolver::instance();
    }
}

// End CView
