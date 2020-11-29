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
            
            if (!isset($_instance)) {
                $html = CApp::component()->mount($this->component)->html();
            } elseif ($_instance->childHasBeenRendered($cachedKey)) {
                $componentId = $_instance->getRenderedChildComponentId($cachedKey);
                $componentTag = $_instance->getRenderedChildComponentTagName($cachedKey);
                $html = CApp::component()->dummyMount($componentId, $componentTag);
                $_instance->preserveRenderedChild($cachedKey);
            } else {
                $response = CApp::component()->mount($this->component);
                $html = $response->html();
                $_instance->logRenderedChild($cachedKey, $response->id(), CApp::component()->getRootElementTagName($html));
            }
            return $html;
        }
    }

    public function js($indent = 0) {
        return '';
    }

}
