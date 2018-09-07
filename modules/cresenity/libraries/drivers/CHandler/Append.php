<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Append_Driver extends CHandler_Driver {

    protected $target;
    protected $method;
    protected $content;
    protected $param;
    protected $param_inputs;
    protected $check_duplicate_selector;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->param_inputs = array();
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

    public function set_target($target) {

        $this->target = $target;

        return $this;
    }

    public function set_method($method) {
        $this->method = $method;
    }

    public function content() {
        return $this->content;
    }

    public function set_check_duplicate_selector($selector) {
        $this->check_duplicate_selector = $selector;
        return $this;
    }

    public function script() {
        $js = parent::script();
        $data_addition = '';

        foreach ($this->param_inputs as $inp) {
            if (strlen($data_addition) > 0)
                $data_addition .= ',';
            $data_addition .= "'" . $inp . "':$('#" . $inp . "').val()";
        }
        $data_addition = '{' . $data_addition . '}';
        $js .= "
            var is_duplicate = 0;
            var check_duplicate = " . (strlen($this->check_duplicate_selector) > 0 ? '1' : '0') . ";
            if(check_duplicate==1){
                if (jQuery('#" . $this->target . "').find('" . $this->check_duplicate_selector . "').length > 0) {
                    is_duplicate = 1;
                }
            }
            if (is_duplicate==0) {
                $.cresenity.append('" . $this->target . "','" . $this->generated_url() . "','" . $this->method . "'," . $data_addition . ");
            }
        ";

        return $js;
    }

}
