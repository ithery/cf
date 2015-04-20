<?php

    defined('SYSPATH') OR die('No direct access allowed.');

    /**
     *
     * @author Raymond Sugiarto
     * @since  Apr 10, 2015
     * @license http://piposystem.com Piposystem
     */
    class CHandler_Remove_Driver extends CHandler_Driver {

        protected $target;
        protected $method;
        protected $content;
        protected $param;
        protected $param_inputs;
        protected $parent;

        public function __construct($owner, $event, $name) {
            parent::__construct($owner, $event, $name);
            $this->method = "get";
            $this->target = $owner;
            $this->content = CHandlerElement::factory();
            $this->param_inputs = array();
        }
        
        public function set_parent($parent){
            $this->parent = $parent;
        }

        public function script() {
            $js = parent::script();
            
            
            $js .= 'jQuery("#' .$this->target .'")';
            if (strlen($this->parent) > 0) {
                $js .= '.parents("' .$this->parent .'")';
            }
            $js .= '.remove();';
            
            return $js;
        }

    }    