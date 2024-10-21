<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:06:27 PM
 */
class CObservable_Listener_Handler_AppendHandler extends CObservable_Listener_Handler {
    use CTrait_Compat_Handler_Driver_Append,
        CObservable_Listener_Handler_Trait_TargetHandlerTrait,
        CObservable_Listener_Handler_Trait_SelectorHandlerTrait,
        CObservable_Listener_Handler_Trait_AjaxHandlerTrait,
        CObservable_Listener_Handler_Trait_BlockerHandlerTrait,
        CObservable_Listener_Handler_Trait_ParamHandlerTrait;

    protected $content;

    protected $param;

    protected $checkDuplicateSelector;

    public function __construct($listener) {
        parent::__construct($listener);
        $this->name = 'Append';
        $this->method = 'get';
        $this->target = '';
        $this->content = CObservable_HandlerElement::factory();
        $this->paramInputs = [];
        $this->paramInputsByName = [];
        $this->paramRequest = [];
        $this->url = '';
        // $this->urlParam = [];
    }

    public function content() {
        return $this->content;
    }

    public function toAttributeArray() {
        return [
            'selector' => $this->getSelector(),
            'url' => $this->generatedUrl(),
            'method' => $this->method,
            'blockType' => $this->blockerType,
        ];
    }

    /**
     * Set duplicate css selector checker.
     *
     * @param string $selector
     *
     * @return $this
     */
    public function setCheckDuplicateSelector($selector) {
        $this->checkDuplicateSelector = $selector;

        return $this;
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
            var isDuplicate = 0;
            var checkDuplicate = ' . (strlen($this->checkDuplicateSelector) > 0 ? '1' : '0') . ';
            if (checkDuplicate == 1) {
                if (jQuery("#' . $this->target . '").find("' . $this->checkDuplicateSelector . '").length > 0) {
                    isDuplicate = 1;
                }
            }

            if (isDuplicate == 0) {
                if (cresenity) {
                    cresenity.append(' . $jsOptions . ');
                } else {
                    $.cresenity.append("' . $this->target . '", "' . $generatedUrl . '", "' . $this->method . '", ' . $dataAddition . ');
                }
            }
         ';

        return $js;
    }
}
