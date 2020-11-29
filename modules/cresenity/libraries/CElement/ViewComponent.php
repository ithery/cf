<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */


class CElement_ViewComponent extends CElement {

    /**
     *
     * @var string
     */
    protected $component;
    
    
    public function __construct($id, $component, $options=[]) {
        parent::__construct($id);
        if($component!=null) {
            $this->setComponent($component);
        }
    }
    
    public function setComponent($component,$options=[]) {
        
        $this->component = $component;
            
    }
 
    public function html($indent = 0) {
        if($this->component!=null) {
            return CApp::component()->getHtml($this->component);
            
        }
    }

    public function js($indent = 0) {
        return '';
    }

}
