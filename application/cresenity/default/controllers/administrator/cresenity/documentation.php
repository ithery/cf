<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */

use Cresenity\Documentation\Documentation;

class Controller_Administrator_Cresenity_Documentation extends CApp_Administrator_Controller_User {
    
    const DOCPATH = DOCROOT.'application/cresenity/default/data/documentation/';
    public function index() {
        $app = CApp::instance();
        
        $app->title('Documentation');
        $app->addBreadcrumb('Cresenity','javascript:;');    
        
        $app->addActionList()->addAction()->setLink($this->controllerUrl().'add')->setLabel('Add')->addClass('btn-primary');        
        $categories = Documentation::instance(static::DOCPATH)->categories();
       
        //transform categories data to jstree view data
        
        
        
        
        
        $app->addTreeView()->setData($categories);
        
        return $app;
    }
    
    public function add() {
        $app = CApp::instance();
        $app->title('Add Documentation');
        $app->addBreadcrumb('Cresenity','javascript:;');    
        $app->addBreadcrumb('Documentation',$this->controllerUrl());
        
        
        
        $form = $app->addForm();
        $widget = $form->addWidget();
        
        $title = $widget->addField()->setLabel('Title')->addControl('title','text');
        
        return $app;
    }
}