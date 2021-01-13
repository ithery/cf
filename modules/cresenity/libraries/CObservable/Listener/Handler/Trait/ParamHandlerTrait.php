<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CObservable_Listener_Handler_Trait_ParamHandlerTrait {
    protected $paramInputs;
    protected $paramInputsByName;
    protected $paramRequest;

    public function addParamInput($inputs) {
        if (!is_array($inputs)) {
            $inputs = [$inputs];
        }
        foreach ($inputs as $inp) {
            $this->paramInputs[] = $inp;
        }
        return $this;
    }

    public function addParamRequest($paramRequest) {
        if (!is_array($paramRequest)) {
            $paramRequest = [$paramRequest];
        }
        foreach ($paramRequest as $reqK => $reqV) {
            $this->paramRequest[$reqK] = $reqV;
        }
        return $this;
    }

    public function addParamInputByName($inputs) {
        if (!is_array($inputs)) {
            $inputs = [$inputs];
        }
        foreach ($inputs as $k => $inp) {
            $this->paramInputsByName[$k] = $inp;
        }
        return $this;
    }

    public function populateParamJson() {
        $dataAddition = '';
        if (is_array($this->paramRequest)) {
            foreach ($this->paramRequest as $reqK => $reqV) {
                if (strlen($dataAddition) > 0) {
                    $dataAddition .= ',';
                }
                $dataAddition .= "'" . $reqK . "':'" . $reqV . "'";
            }
        }
        if (is_array($this->paramInputs)) {
            foreach ($this->paramInputs as $inp) {
                if (strlen($dataAddition) > 0) {
                    $dataAddition .= ',';
                }
                $dataAddition .= "'" . $inp . "':cresenity.value('#" . $inp . "')";
            }
        }
        if (is_array($this->paramInputsByName)) {
            foreach ($this->paramInputsByName as $k => $inp) {
                if (strlen($dataAddition) > 0) {
                    $dataAddition .= ',';
                }
                $dataAddition .= "'" . $k . "':cresenity.value('" . $inp . "')";
            }
        }
        $dataAddition = '{' . $dataAddition . '}';
        return $dataAddition;
    }
}
