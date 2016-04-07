<?php

    defined('SYSPATH') OR die('No direct access allowed.');

    class CMobile_Handler_Closepage extends CMobile_HandlerDriver {

        protected $target;
        protected $method;
        protected $content;
        protected $param;
        protected $title;
        protected $actions;
        protected $param_inputs;
        protected $param_request;
        protected $reload_page;
        protected $callback;
        protected $js_class;
        protected $js_class_manual;

        public function __construct($owner, $event, $name) {
            parent::__construct($owner, $event, $name);
            $this->method = "get";
            $this->target = "";
            $this->content = CHandlerElement::factory();
            $this->actions = CActionList::factory();
            $this->param_inputs = array();
            $this->param_request=array();
            $this->title = '';
            $this->js_class = null;
            $this->js_class_manual = null;
        }
       
        
        public function set_reload_page($reload_page){
            $this->reload_page = $reload_page;
            return $this;
        }
        
        public function set_callback(callable $callback){
            $this->callback = $callback;
            return $this;
        }

        public function set_target($target) {
            $this->target = $target;
            return $this;
        }

        public function content() {
            return $this->content;
        }

        public function script() {
            $js = parent::script();
            if (strlen($this->target) == 0) {
                $this->target = "modal_opt_" . $this->event . "_" . $this->owner . "_dialog";
            }
            $js.= "
                    $.cresenity.close_page('" . $this->target . "');
                ";
            return $js;
        }

    }
    