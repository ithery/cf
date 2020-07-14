<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CObservable_Listener_Handler_AjaxHandler extends CObservable_Listener_Handler {

    use CObservable_Listener_Handler_Trait_AjaxHandlerTrait,
        CObservable_Listener_Handler_Trait_TargetHandlerTrait,
        CObservable_Listener_Handler_Trait_ParamHandlerTrait;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->name = "Ajax";
        $this->method = "post";
        $this->url = "";
    }

    public function js() {
        $dataAddition =  $this->populateParamJson();
        $generatedUrl = $this->generatedUrl();
        $optionsJson = "{";
        $optionsJson .= "url:'" . $generatedUrl . "',";
        $optionsJson .= "method:'" . $this->method . "',";
        $optionsJson .= "dataAddition:" . $dataAddition . ",";
        if ($this->haveCompleteListener()) {
            $optionsJson .= "onComplete: " . $this->getCompleteListener()->js() . ",";
        }
        if ($this->haveSuccessListener()) {
            $optionsJson .= "onSuccess: " . $this->getSuccessListener()->js() . ",";
        }
        $optionsJson .= 'handleJsonResponse: true,';
        $optionsJson .= "}";
        $js = '';
        $js .= "
            cresenity.ajax(" . $optionsJson . ");;
         ";
        return $js;
    }

}
