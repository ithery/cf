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
        CObservable_Listener_Handler_Trait_SelectorHandlerTrait,
        CObservable_Listener_Handler_Trait_AjaxHandlerTrait,
        CObservable_Listener_Handler_Trait_BlockerHandlerTrait,
        CObservable_Listener_Handler_Trait_ParamHandlerTrait;

    protected $content;
    protected $param;
    

    

    public function __construct($listener) {
        parent::__construct($listener);
        $this->method = "get";
        $this->target = "";
        $this->content = CHandlerElement::factory();
        $this->paramInputs = array();
        $this->paramInputsByName = array();
        $this->paramRequest = array();
        $this->name = 'Reload';
        $this->url = "";
        $this->urlParam = array();
    }


    public function content() {
        return $this->content;
    }

    public function js() {
        $js = '';
        $dataAddition =  $this->populateParamJson();
        
        $generatedUrl = $this->generatedUrl();
        $jsOptions = "{";
        $jsOptions .= "selector:'" . $this->getSelector() . "',";
        $jsOptions .= "url:'" . $generatedUrl . "',";
        $jsOptions .= "method:'" . $this->method . "',";
        $jsOptions .= "dataAddition:" . $dataAddition . ",";
        $jsOptions .= "blockType:'" . $this->getBlockerType() . "',";

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
    
    
    public function toAttributeArray() {
        $attributes = [];
        $attributes['method']=$this->method;
        $attributes['url']=$this->generatedUrl();
        $attributes['dataAddition']=$this->populateParamJson();
        $attributes['blockHtml']=$this->getBlockerHtml();
        $attributes['blockType']=$this->getBlockerType();
        return $attributes;
        
    }

}
