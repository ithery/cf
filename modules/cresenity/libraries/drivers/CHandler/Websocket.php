<?php

    defined('SYSPATH') OR die('No direct access allowed.');

    class CHandler_Websocket_Driver extends CHandler_Driver {

        protected $target;
        protected $js;
        protected $param_inputs;
        protected $action_socket;
        protected $param_vars;

        public function __construct($owner, $event, $name) {
            parent::__construct($owner, $event, $name);
            
            $this->action_socket = 'send';
            $this->param_vars = array();
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
        
        public function add_param_vars($param_vars) {
            if (!is_array($param_vars)) {
                $inputs = array($param_vars);
            }
            foreach ($param_vars as $key => $value) {
                $this->param_vars[$key] = $value;
                
            }
            return $this;
        }

        public function script() {
            $js = parent::script();
            $js .= $this->js;
            $data_addition = '';

            foreach ($this->param_inputs as $inp) {
                if (strlen($data_addition) > 0) $data_addition.=',';
                $data_addition .= "'" . $inp . "':$('#" . $inp . "').val()";
            }
            $data_addition = "{
                            'act': '" . $this->action_socket . "',
                            " . $data_addition . "
                        }";
            $js.= "
                    if (websocket.readyState == 1) {
			websocket.send(JSON.stringify(" . $data_addition . "));
                    }
                    else {
                        $.cresenity.message('error','Socket[' + websocket.readyState + '] error connection.');
                    }
		";

            return $js;
        }

        function set_action_socket($action_socket) {
            $this->action_socket = $action_socket;
            return $this;
        }

        function get_to_app_id() {
            return $this->to_app_id;
        }

        function get_to_org_id() {
            return $this->to_org_id;
        }

        function get_to_username() {
            return $this->to_username;
        }

        function set_to_app_id($to_app_id) {
            $this->to_app_id = $to_app_id;
            return $this;
        }

        function set_to_org_id($to_org_id) {
            $this->to_org_id = $to_org_id;
            return $this;
        }

        function set_to_username($to_username) {
            $this->to_username = $to_username;
            return $this;
        }

        }
    