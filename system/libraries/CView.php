<?php

defined('SYSPATH') OR die('No direct access allowed.');

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
     * @param   string  view name
     * @param   array   pre-load data
     * @param   string  type of file: html, css, js, etc.
     * @return  object
     */
    public static function factory($name = NULL, $data = [], $mergeData = []) {
        if ($name == null) {
            return CView_factory::instance();
        }
        return CView_Factory::instance()->make($name, $data);
        //return new CView($name, $data, $type);
    }

    /**
     * Check a CView is exists.
     *
     * @param   string  view name
     * @return  boolean
     */
    public static function exists($name) {
        return CView_Factory::instance()->exists($name);
    }

}

// End CView