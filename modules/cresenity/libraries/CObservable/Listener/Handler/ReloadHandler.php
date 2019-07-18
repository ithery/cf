<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 1:22:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_ReloadHandler extends CObservable_Listener_Handler {

    use CTrait_Compat_Handler_Driver_Reload,
        CObservable_Listener_Handler_Trait_TargetHandlerTrait,
        CObservable_Listener_Handler_Trait_AjaxHandlerTrait;

    protected $content;
    protected $param;
    protected $param_inputs;
    protected $param_inputs_by_name;
    protected $paramRequest;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->param_inputs = array();
        $this->param_inputs_by_name = array();
        $this->paramRequest = array();
        $this->name = 'Reload';
        $this->url = "";
        $this->urlParam = array();
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
        return $this;
    }

    public function content() {
        return $this->content;
    }

    public function js() {
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
        $generatedUrl = $this->generatedUrl();
        $jsOptions = "{";
        $jsOptions .= "selector:'#" . $this->target . "',";
        $jsOptions .= "url:'" . $generatedUrl . "',";
        $jsOptions .= "method:'" . $this->method . "',";
        $jsOptions .= "dataAddition:" . $dataAddition . ",";

        $jsOptions .= "}";




        $js .= "
            if(cresenity) {
                cresenity.reload(" . $jsOptions . ");
            } else {
                $.cresenity.reload('" . $this->target . "','" . $generatedUrl . "','" . $this->method . "'," . $dataAddition . ");
            }
         ";

        return $js;
    }

}
