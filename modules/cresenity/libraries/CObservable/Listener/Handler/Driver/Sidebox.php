<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 18, 2019, 11:52:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_Driver_Sidebox extends CObservable_Listener_Handler_Driver {

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

    public function addParamInput($inputs) {
        if (!is_array($inputs)) {
            $inputs = array($inputs);
        }
        foreach ($inputs as $inp) {
            $this->param_inputs[] = $inp;
        }
        return $this;
    }

    public function addParamInputByName($inputs) {
        if (!is_array($inputs)) {
            $inputs = array($inputs);
        }
        foreach ($inputs as $k => $inp) {
            $this->param_inputs_by_name[$k] = $inp;
        }
        return $this;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function content() {
        return $this->content;
    }

    public function script() {
        $js = '';
        $dataAddition = '';

        foreach ($this->param_inputs as $inp) {
            if (strlen($dataAddition) > 0) {
                $dataAddition .= ',';
            }
            $dataAddition .= "'" . $inp . "':$.cresenity.value('#" . $inp . "')";
        }
        foreach ($this->param_inputs_by_name as $k => $inp) {
            if (strlen($dataAddition) > 0) {
                $dataAddition .= ',';
            }
            $dataAddition .= "'" . $k . "':$.cresenity.value('" . $inp . "')";
        }
        $dataAddition = '{' . $dataAddition . '}';
        $js .= "
            $.cresenity.reload('" . $this->target . "','" . $this->generated_url() . "','" . $this->method . "'," . $dataAddition . ");
         ";

        return $js;
    }

}
