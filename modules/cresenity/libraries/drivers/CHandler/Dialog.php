<?php

    defined('SYSPATH') OR die('No direct access allowed.');

    class CHandler_Dialog_Driver extends CHandler_Driver {

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
        protected $custom_id;

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
            $this->custom_id = "";
        }
       
        
        public function set_reload_page($reload_page){
            $this->reload_page = $reload_page;
            return $this;
        }
        
        public function set_callback(callable $callback){
            $this->callback = $callback;
            return $this;
        }

        public function set_title($title) {
            $this->title = $title;
        }

        public function set_target($target) {
            $this->target = $target;
            return $this;
        }
        
        public function set_js_class($js_class) {
            //set js class manual
            $this->js_class_manual = $js_class;
            return $this;
        }

        public function add_param_input($inputs) {
            if (!is_array($inputs)) {
                $inputs = array($inputs);
            }
            foreach ($inputs as $inp) {
                $this->param_inputs[] = $inp;
            }
            return $this;
        }
        
        public function set_custom_id($param) {
            $this->custom_id = $param;
            return $this;
        }
        
        public function add_param_request($param_request) {
		if(!is_array($param_request)) {
			$param_request = array($param_request);
		}
		foreach($param_request as $req_k => $req_v) {
			$this->param_request[$req_k] = $req_v;
		}
		return $this;
	}

        public function set_method($method) {
            $this->method = $method;
        }

        public function content() {
            return $this->content;
        }

        public function script() {
            $js = parent::script();
            if (strlen($this->target) == 0) {
                $this->target = "modal_opt_" . $this->event . "_" . $this->owner . "_dialog";
            }

            $data_addition = '';

            foreach ($this->param_inputs as $inp) {
                if (strlen($data_addition) > 0) $data_addition.=',';
                $data_addition.="'" . $inp . "':$.cresenity.value('#" . $inp . "')";
            }
            foreach($this->param_request as $req_k => $req_v) {
			if(strlen($data_addition)>0) $data_addition.=',';
			$data_addition.= "'".$req_k."':'".$req_v."'";
		}
            $data_addition = '{' . $data_addition . '}';
            /*
              $js.= "
              var modal_opt_".$this->event."_".$this->owner." = {
              id: 'modal_opt_".$this->event."_".$this->owner."_dialog', // id which (if specified) will be added to the dialog to make it accessible later
              autoOpen: true , // Should the dialog be automatically opened?
              title: '".$this->title."',
              content: '".$this->generated_url()."',
              buttons: {

              },
              closeOnOverlayClick: true , // Should the dialog be closed on overlay click?
              closeOnEscape: true , // Should the dialog be closed if [ESCAPE] key is pressed?
              removeOnClose: true , // Should the dialog be removed from the document when it is closed?
              showCloseHandle: true , // Should a close handle be shown?
              initialLoadText: '' // Text to be displayed when the dialogs contents are loaded
              }
              jQuery('<div/>').dialog2(modal_opt_".$this->event."_".$this->owner.");
              ";
             */
            $js_class = ccfg::get('js_class');
            if (strlen($js_class) > 0) {
                $this->js_class = $js_class;
            }
            if($this->js_class_manual != null) {
                $this->js_class = $this->js_class_manual;
            }
            if (strlen($this->js_class) > 0 && $this->js_class != 'cresenity') {
                if ($this->content instanceof CHandlerElement) {
                    $content = $this->content->html();
                }
                else {
                    $content = $this->content;
                }
                $content = addslashes($content);
                $content = str_replace("\r\n", "", $content);
                if(strlen(trim($content))==0) {
                    $content = $this->generated_url();
                }
                $js .= "
                    $." .$this->js_class .".show_dialog('" .$this->target ."','" .$this->title ."','" .$content ."', '".$this->custom_id."');
                    ";
            }
            else {
                $js.= "
                        $.cresenity.show_dialog('" . $this->target . "','" . $this->generated_url() . "','" . $this->method . "','" . $this->title . "'," . $data_addition . ");
                    ";
            }
            return $js;
        }

    }
    