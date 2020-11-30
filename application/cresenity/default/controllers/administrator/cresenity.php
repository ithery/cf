<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 28, 2020 
 * @license Ittron Global Teknologi
 */

use Cresenity\Documentation\Documentation;

class Controller_Administrator_Cresenity extends CApp_Administrator_Controller_User {
    
    const DOCPATH = DOCROOT.'application/cresenity/default/data/documentation/';
    public function documentation() {
        $app = CApp::instance();
        
        $app->title('Documentation');
        $app->addBreadcrumb('Cresenity','javascript:;');
        
        $categories = Documentation::instance(static::DOCPATH)->categories();
       
        //transform categories data to jstree view data
        
        
        
        
        
        $app->addTreeView()->setData($categories);
        
        echo $app->render();    
    }
}