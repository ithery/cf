<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * CF Controller class. The controller class must be extended to work
 * properly, so this class is defined as abstract.
 */
abstract class CController {
    // Allow all controllers to run in production by default
    const ALLOW_PRODUCTION = true;

    protected $baseUri;

    /**
     * Loads URI, and Input into this controller.
     *
     * @return void
     */
    public function __construct() {
        if (CF::$instance == null) {
            // Set the instance to the first controller loaded
            CF::$instance = $this;
        }

        // URI should always be available
        $this->uri = URI::instance();

        // Input should always be available
        $this->input = Input::instance();

        $this->baseUri = CFRouter::controllerUri();
    }

    /**
     * Handles methods that do not exist.
     *
     * @param string $method method name
     * @param array  $args   arguments
     *
     * @return void
     */
    public function __call($method, $args) {
        // Default to showing a 404 page
        CF::show404();
    }

    public static function controllerUrl() {
        $class = get_called_class();
        $classExplode = explode('_', $class);
        $classExplode = array_map(function ($item) {
            return cstr::camel($item);
        }, $classExplode);
        $url = curl::base() . implode(array_slice($classExplode, 1), '/') . '/';

        return $url;
    }
}

// End Controller Class
