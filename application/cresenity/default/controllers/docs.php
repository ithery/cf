<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Dec 5, 2020 
 * @license Ittron Global Teknologi
 */

Class Controller_Docs extends CController {

    public function __construct() {
        parent::__construct();
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setTheme('cresenity-docs');
        $app->setView('docs');
    }
    
    public function index() {
        return $this->page();
    }

    public function page($slug = null) {
        $app = CApp::instance();
        
        $app->setView('docs');
        return $app;
    }

}
