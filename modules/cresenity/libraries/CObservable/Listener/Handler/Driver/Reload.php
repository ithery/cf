<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 4:04:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_Driver_Reload extends CObservable_Listener_Handler_Driver {

    use CTrait_Compat_Handler_Driver_Reload;

    protected $method;
    protected $content;
    protected $param;
    protected $param_inputs;
    protected $param_inputs_by_name;
    protected $paramRequest;

    public function __construct($owner, $event, $name) {
        parent::__construct($owner, $event, $name);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->param_inputs = array();
        $this->param_inputs_by_name = array();
        $this->paramRequest = array();
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

    public function addParamRequest($paramRequest) {
        if (!is_array($paramRequest)) {
            $paramRequest = array($paramRequest);
        }
        foreach ($paramRequest as $reqK => $reqV) {
            $this->paramRequest[$reqK] = $reqV;
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
        foreach ($this->paramRequest as $reqK => $reqV) {
            if (strlen($dataAddition) > 0) {
                $dataAddition .= ',';
            }
            $dataAddition .= "'" . $reqK . "':'" . $reqV . "'";
        }

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
            $.cresenity.reload('" . $this->target . "','" . $this->generatedUrl() . "','" . $this->method . "'," . $dataAddition . ");
         ";

        return $js;
    }

}
