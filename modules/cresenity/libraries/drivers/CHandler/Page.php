<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Page_Driver extends CHandler_Driver {

    protected $target;
    protected $method;
    protected $content;
    protected $param;
    protected $param_inputs;
    protected $param_inputs_by_name;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->param_inputs = array();
        $this->param_inputs_by_name = array();
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

    public function add_param_input_by_name($inputs) {
        if (!is_array($inputs)) {
            $inputs = array($inputs);
        }
        foreach ($inputs as $k => $inp) {
            $this->param_inputs_by_name[$k] = $inp;
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

    public function script() {
        $js = parent::script();
        $data_addition = '';

        foreach ($this->param_inputs as $inp) {
            if (strlen($data_addition) > 0)
                $data_addition.=',';
            $data_addition.="'" . $inp . "':$.cresenity.value('#" . $inp . "')";
        }
        foreach ($this->param_inputs_by_name as $k => $inp) {
            if (strlen($data_addition) > 0)
                $data_addition.=',';
            $data_addition.="'" . $k . "':$.cresenity.value('" . $inp . "')";
        }
        $data_addition = '{' . $data_addition . '}';
        $js.= "
                $.cresenity.reload('" . $this->target . "','" . $this->generated_url() . "','" . $this->method . "'," . $data_addition . ");
             ";

        return $js;
    }

}
