<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * CF Controller class. The controller class must be extended to work
 * properly, so this class is defined as abstract.
 *
 */
abstract class CController {

    // Allow all controllers to run in production by default
    const ALLOW_PRODUCTION = TRUE;

    /**
     * Loads URI, and Input into this controller.
     *
     * @return  void
     */
    public function __construct() {
        if (CF::$instance == NULL) {
            // Set the instance to the first controller loaded
            CF::$instance = $this;
        }

        // URI should always be available
        $this->uri = URI::instance();

        // Input should always be available
        $this->input = Input::instance();
    }

    /**
     * Handles methods that do not exist.
     *
     * @param   string  method name
     * @param   array   arguments
     * @return  void
     */
    public function __call($method, $args) {
        // Default to showing a 404 page
        CFEvent::run('system.404');
    }

    
}

// End Controller Class