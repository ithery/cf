<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 1:22:39 PM
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
        $this->method = 'get';
        $this->target = '';
        $this->content = CHandlerElement::factory();
        $this->paramInputs = [];
        $this->paramInputsByName = [];
        $this->paramRequest = [];
        $this->name = 'Reload';
        $this->url = '';
        $this->urlParam = [];
    }

    public function content() {
        return $this->content;
    }

    public function js() {
        $js = '';
        $dataAddition = $this->populateParamJson();

        $generatedUrl = $this->generatedUrl();
        $jsOptions = '{';
        $jsOptions .= "selector:'" . $this->getSelector() . "',";
        $jsOptions .= "url:'" . $generatedUrl . "',";
        $jsOptions .= "method:'" . $this->method . "',";
        $jsOptions .= 'dataAddition:' . $dataAddition . ',';
        $jsOptions .= "blockType:'" . $this->getBlockerType() . "',";

        $jsOptions .= '}';

        $js .= '
            if(cresenity) {
                cresenity.reload(' . $jsOptions . ");
            } else {
                $.cresenity.reload('" . $this->target . "','" . $generatedUrl . "','" . $this->method . "'," . $dataAddition . ');
            }
         ';

        return $js;
    }
}
