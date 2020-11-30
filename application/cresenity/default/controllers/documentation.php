<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
Class Controller_Documentation extends CController {

    public function index() {
        return $this->page();
    }
    
    public function page($slug=null) {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setTheme('cfdocs');
        
        $app->setView('documentation');
        
        return $app;
        
    }

}
